<?php


namespace Webkul\IyzicoPayment\Helpers;

/**
 * Iyzico payment api and secret key
 */

class IyzicoApi
{
    public static function options() {
        $options = new \Iyzipay\Options();
        $options->setApiKey("env('IYZICO_API_KEY')");
        $options->setSecretKey("env('IYZICO_SECRET_KEY')");
        $options->setBaseUrl("env('IYZICO_BASE_URL')");
        return $options;
    }
}
