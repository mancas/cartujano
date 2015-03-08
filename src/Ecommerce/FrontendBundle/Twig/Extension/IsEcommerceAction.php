<?php
namespace Ecommerce\FrontendBundle\Twig\Extension;

use Ecommerce\FrontendBundle\Twig\Extension\CustomTwigExtension;

class IsEcommerceAction extends CustomTwigExtension
{
    public function getFunctions()
    {
        return array('isEcommerceAction' => new \Twig_Function_Method($this, 'isEcommerceAction'));
    }

    public function isEcommerceAction($path)
    {
        if (strpos($path, '/e/') !== FALSE) {
            return true;
        }

        return false;
    }


    public function getName()
    {
        return 'isEcommerceAction_extension';
    }
}