<?php

namespace Webkul\Iyzico\Payment;

use Illuminate\Support\Facades\Storage;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;
use Webkul\Payment\Payment\Payment;

class Iyzico extends Payment
{
    /**
     * Payment method code
     */
    protected string $code = 'iyzico';

    /**
     * Paypal partner attribution id.
     *
     * @var string
     */
    protected $iyzicoPartnerAttributionId = 'Bagisto_Cart';

    /**
     * Refund order.
     *
     */
    public function refundOrder($captureId, $body = [])
    {
        $request = new CapturesRefundRequest($captureId);

        $request->headers['Iyzico-Attribution-Id'] = $this->iyzicoPartnerAttributionId;
        $request->body = $body;

        return $this->client()->execute($request);
    }

    public function getRedirectUrl(): string
    {
        return route('iyzico.redirect');
    }

    /**
     * Returns payment method image
     */
    public function getImage(): string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/money-transfer.png', 'shop');
    }
}
