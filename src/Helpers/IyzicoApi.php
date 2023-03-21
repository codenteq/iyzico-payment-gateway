<?php


namespace Webkul\Iyzico\Helpers;

/**
 * Iyzico payment api and secret key
 */
class IyzicoApi
{
    public static function options()
    {
        $options = new \Iyzipay\Options();
        $options->setApiKey(env('IYZICO_API_KEY', 'null'));
        $options->setSecretKey(env('IYZICO_SECRET_KEY', 'null'));
        $options->setBaseUrl(env('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'));
        return $options;
    }
}
