<?php

namespace Ecommerce\PayPalBundle\Paypal;

use Ecommerce\ItemBundle\Entity\Item;

class Paypal
{
    protected $apiName;
    protected $apiKey;
    protected $apiCertificate;
    protected $environment;
    protected $currency;
    protected $tipoPago;
    protected $delivery;
    protected $apiVersion;
    protected $pagoTarjeta;
    protected $localeCode;
    protected $taxes;

    public function __construct($apiName, $apiKey, $apiCertificate, $environment, $currency, $tipoPago, $delivery, $version, $pagoTarjeta, $localeCode, $container)
    {
        $this->apiName = $apiName;
        $this->apiKey = $apiKey;
        $this->apiCertificate = $apiCertificate;
        if ($environment == 'beta-sandbox') {
            $environment = 'sandbox.paypal';
        }
        $this->environment = $environment;
        $this->currency = $currency;
        $this->tipoPago = $tipoPago;
        $this->delivery = $delivery;
        $this->apiVersion = $version;
        $this->pagoTarjeta = $pagoTarjeta;
        $this->localeCode = $localeCode;
        //$this->taxes = $container->getParameter('taxes_es')/100;
        $this->taxes = Item::TAX_ES/100;
    }

    public function pay($paymentAmount, $paymentTaxes, $deliveryAmount, $desc, $urlAccept, $urlCancel)
    {
        $urlAccept = $this->sanitizeUrl($urlAccept);
        $urlCancel = $this->sanitizeUrl($urlCancel);
        $nvpStr = $this->getNvpStr($paymentAmount, $paymentTaxes,$deliveryAmount, $desc, $urlAccept, $urlCancel);

        $httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $nvpStr);
        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
            $token = urldecode($httpParsedResponseAr["TOKEN"]);
            $payPalURL = "https://www." . $this->environment . ".com/webscr&cmd=_express-checkout&token=$token";
        } else {
            $payPalURL = $urlCancel;
        }

        return array('ok' => true, 'url' => $payPalURL);
    }

    /**
     * public for testing :(
     *
     * @param string $url
     *
     * @return string
     */
    public function sanitizeUrl($url)
    {
        if (strpos($url, 'http') === false) {
            $url = 'http://' . $url;
        }

        return $url;
    }

    private function getNvpStr($paymentAmount, $paymentTaxes, $deliveryAmount, $desc, $urlAccept, $urlCancel)
    {
        $currencyID = urlencode($this->currency);
        $paymentType = urlencode($this->tipoPago);
        $noShipping = urlencode($this->delivery);
        $sole = urlencode($this->pagoTarjeta);
        $localeCode = urlencode($this->localeCode);
        $paymentTotal = urlencode($paymentAmount + $paymentTaxes + $deliveryAmount);

        if ($deliveryAmount > 0) {
            $nvpStr = "&PAYMENTREQUEST_0_AMT=$paymentTotal&L_PAYMENTREQUEST_0_DESC0=$desc&PAYMENTREQUEST_0_ITEMAMT=$paymentAmount&L_PAYMENTREQUEST_0_AMT0=$paymentAmount&PAYMENTREQUEST_0_TAXAMT=$paymentTaxes&L_PAYMENTREQUEST_0_QTY0=1&ReturnUrl=$urlAccept&CANCELURL=$urlCancel&PAYMENTACTION=$paymentType&PAYMENTREQUEST_0_CURRENCYCODE=$currencyID&PAYMENTREQUEST_0_SHIPPINGAMT=$deliveryAmount&SOLUTIONTYPE=$sole&LOCALECODE=$localeCode";
        } else {
            $nvpStr = "&PAYMENTREQUEST_0_AMT=$paymentTotal&L_PAYMENTREQUEST_0_DESC0=$desc&PAYMENTREQUEST_0_ITEMAMT=$paymentAmount&L_PAYMENTREQUEST_0_AMT0=$paymentAmount&PAYMENTREQUEST_0_TAXAMT=$paymentTaxes&L_PAYMENTREQUEST_0_QTY0=1&ReturnUrl=$urlAccept&CANCELURL=$urlCancel&PAYMENTACTION=$paymentType&PAYMENTREQUEST_0_CURRENCYCODE=$currencyID&NOSHIPPING=$noShipping&SOLUTIONTYPE=$sole&LOCALECODE=$localeCode";
        }

        return $nvpStr;
    }

    public function PPHttpPost($methodName, $nvpStr)
    {
        $env = $this->environment;
        $apiUserName = urlencode($this->apiName);
        $apiPassword = urlencode($this->apiKey);
        $apiSignature = urlencode($this->apiCertificate);
        $apiEndpoint = "https://api-3t.$env.com/nvp";

        $version = urlencode($this->apiVersion);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $nvpreq = "METHOD=$methodName&VERSION=$version&PWD=$apiPassword&USER=$apiUserName&SIGNATURE=$apiSignature$nvpStr";

        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            exit("$methodName failed: " . curl_error($ch) . '('. curl_errno($ch) . ')');
        }

        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }
ldd($httpParsedResponseAr);
        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $apiEndpoint.");
        }

        return $httpParsedResponseAr;
    }

    public function getApiName()
    {
        return $this->apiName;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getApiCertificate()
    {
        return $this->apiCertificate;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getDelivery()
    {
        return $this->delivery;
    }

    public function getVersion()
    {
        return $this->apiVersion;
    }

    public function getTipoPago()
    {
        return $this->tipoPago;
    }

    public function getPagoTarjeta()
    {
        return $this->pagoTarjeta;
    }

    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    public function getTaxes()
    {
        return $this->taxes;
    }
}