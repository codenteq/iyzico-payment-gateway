<?php


namespace Webkul\IyzicoPayment\Helpers;

/**
 * Iyzico payment api and secret key
 */

class IyzicoApi
{
    public static function options() {
        $options = new \Iyzipay\Options();
        $options->setApiKey("api-key");
        $options->setSecretKey("secret-key");
        $options->setBaseUrl("https://sandbox-api.iyzipay.com");
        return $options;
    }
}