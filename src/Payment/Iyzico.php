<?php

namespace Webkul\Iyzico\Payment;

use Webkul\Payment\Payment\Payment;

class Iyzico extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'iyzico';

    public function getRedirectUrl(): string
    {
        return route('iyzico.redirect');
    }
}