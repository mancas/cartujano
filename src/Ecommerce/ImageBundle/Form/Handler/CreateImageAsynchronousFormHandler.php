<?php

namespace Ecommerce\ImageBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Ecommerce\ImageBundle\Form\Handler\ImageManager;
use Ecommerce\ImageBundle\Entity\Image;
use Ecommerce\ItemBundle\Entity\Item;
use Ecommerce\ImageBundle\Entity\ImageItem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class CreateImageAsynchronousFormHandler
{
    private $imageManager;
    private $validator;
    private $container;

    public function __construct(ImageManager $imageManager, RecursiveValidator $validator, ContainerInterface $container)
    {
        $this->imageManager = $imageManager;
        $this->validator = $validator;
        $this->container = $container;
    }

    public function handleAjaxUpload(UploadedFile $img, $item, $request)
    {
        if ($request->getMethod() == 'POST') {
            $imageConstraint = new \Symfony\Component\Validator\Constraints\Image();
            $imageConstraint->minWidthMessage = 'La imagen debe tener un mínimo de ' . Image::MIN_WIDTH . ' píxeles de ancho';
            $imageConstraint->minWidth = Image::MIN_WIDTH;
            $imageConstraint->minWidthMessage =  'La imagen debe tener un mínimo de ' . Image::MIN_HEIGHT . ' píxeles de alto';
            $imageConstraint->minHeight = Image::MIN_HEIGHT;
            $imageConstraint->minWidthMessage =  'La imagen debe tener un mínimo de ' . Image::MIN_HEIGHT . ' píxeles de alto';
            // TODO: fix it
            //$imageConstraint->maxSizeMessage = Image::ERROR_MESSAGE;
            //$imageConstraint->maxSize = Image::MAX_SIZE;

            $errorList = $this->validator->validateValue($img, $imageConstraint);

            if (count($errorList) == 0) {
                if (get_class($img) == "Symfony\\Component\\HttpFoundation\\File\\UploadedFile") {
                    try {
                        $image = new ImageItem();
                        if (!$item->getImageMain()) {
                            $image->setMain(true);
                        }
                        $image->setItem($item);
                        $image->setFile($img);
                        $this->imageManager->saveImage($image);
                        $this->imageManager->createImage($image);
                        $path = $this->container->get('templating.helper.assets')->getUrl($image->getImageThumbnail()->getWebFilePath());

                        return array('id'   => $image->getId(),
                            'path' => $path);

                    } catch (\Exception $e) {
                        $this->imageManager->removeImage($image);
                        ld($e);
                        return false;
                    }
                } else {
                    return false;
                }

            } else {
                return $errorList;
            }
        }
        return false;
    }

}