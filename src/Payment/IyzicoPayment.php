<?php

namespace Webkul\IyzicoPayment\Payment;

use Webkul\Payment\Payment\Payment;

class IyzicoPayment extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'iyzicopayment';

    public function getRedirectUrl()
    {
        return route('iyzico.redirect');
    }
}