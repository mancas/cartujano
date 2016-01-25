<?php

namespace Ecommerce\FrontendBundle\Controller;

use Ecommerce\FrontendBundle\Form\ContactType;
use Ecommerce\FrontendBundle\Model\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class FrontendController extends CustomController
{
    const ITEMS_LIMIT_DQL = 12;

    public function indexAction()
    {
        return $this->render('FrontendBundle:Pages:home.html.twig');
    }

    public function ecommerceHomepageAction()
    {
        $em = $this->getEntityManager();
        $items = $em->getRepository('ItemBundle:Item')->findCommercialItemsDQL();
        $shipments = $this->getEntityManager()->getRepository('ItemBundle:Shipment')->findAllShipmentOptions();
        $shipment = null;
        if (count($shipments) > 0) {
            $shipment = $shipments[0];
        }

        $this->setCurrentCartIfNeeded();

        return $this->render('FrontendBundle:Pages:ecommerce.html.twig', array('items' => $items, 'shipment' => $shipment));
    }

    /**
     * @Template("FrontendBundle:Commons:category-navbar.html.twig")
     *
     * @return array
     */
    public function categoryNavAction()
    {
        $em = $this->getEntityManager();
        $categories = $em->getRepository('CategoryBundle:Category')->findCategoriesDQL();

        return array('categories' => $categories);
    }

    public function policyAction()
    {
        return $this->render('FrontendBundle:Pages:policy.html.twig');
    }

    public function useTermsAction()
    {
        return $this->render('FrontendBundle:Pages:use-terms.html.twig');
    }

    public function whoWeAreAction()
    {
        return $this->render('FrontendBundle:Pages:who-we-are.html.twig');
    }

    public function cookiesPolicyAction()
    {
        return $this->render('FrontendBundle:Pages:cookies.html.twig');
    }

    public function searchAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $search = $request->request->get('search');
            if (isset($search) && strlen($search) > 0) {
                $items = $this->getEntityManager()->getRepository('ItemBundle:Item')->findItemsBySearchText($search);
                return $this->render('FrontendBundle:Pages:search.html.twig', array('items' => $items, 'search' => $search));
            }
        }
        return $this->redirect($this->generateUrl('frontend_homepage'));
    }

    public function contactAction(Request $request)
    {
        $form = $this->createForm(new ContactType(), new Contact());
        $formHandler = $this->get('contact.contact_form_handler');

        $message = $formHandler->handle($form, $request);
        if ($message)
            $this->setTranslatedFlashMessage($message);

        return $this->render('FrontendBundle:Pages:contact.html.twig', array('form' => $form->createView()));
    }
}
