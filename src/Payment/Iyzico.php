<?php

namespace Webkul\Iyzico\Payment;

use Illuminate\Support\Facades\Storage;
use Webkul\Payment\Payment\Payment;

class Iyzico extends Payment
{
    /**
     * Payment method code
     */
    protected string $code = 'iyzico';

    public function getRedirectUrl(): string
    {
        return route('iyzico.redirect');
    }

    /**
     * Returns payment method image
     *
     * @return string
     */
    public function getImage(): string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/iyzico.svg', 'iyzico');
    }
}
