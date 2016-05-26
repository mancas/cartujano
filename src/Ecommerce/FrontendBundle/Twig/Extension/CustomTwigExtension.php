<?php

namespace Ecommerce\FrontendBundle\Twig\Extension;

use Ecommerce\PaymentBundle\Entity\Payment;
use Ecommerce\PaymentBundle\Entity\Transfer;
use Ecommerce\PayPalBundle\Entity\PaypalPayment;

class CustomTwigExtension extends \Twig_Extension
{
    protected $environment;

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
    }

    public function getName()
    {
    }

    public function getTests ()
    {
        return [
            new \Twig_SimpleTest('transfer', function (Payment $payment) { return $payment instanceof Transfer; }),
            new \Twig_SimpleTest('paypal', function (Payment $payment) { return $payment instanceof PaypalPayment; })
        ];
    }

    protected function printScript($script, $printDocumentReady=false)
    {
        if ($printDocumentReady) {
            printf('$(document).ready(function(){%s});', $script);
        } else {
            print($script);
        }
    }

    protected function getJqueryClose()
    {
        return "});";
    }
}
