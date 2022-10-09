<?php


namespace Webkul\IyzicoPayment\Helpers;

/**
 * Iyzico payment api and secret key
 */

class IyzicoApi
{
    public static function options() {
        $options = new \Iyzipay\Options();
        $options->setApiKey("env('API_KEY')");
        $options->setSecretKey("env('SECRET_KEY')");
        $options->setBaseUrl("env('BASE_URL')");
        return $options;
    }
}