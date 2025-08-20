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
     * Check if the payment method is available
     */
    public function isAvailable(): bool
    {
        $allowGuestCheckout = (bool) core()->getConfigData('sales.checkout.shopping_cart.allow_guest_checkout');
        $isActive = (bool) $this->getConfigData('active');

        if ($allowGuestCheckout || !$isActive) {
            return false;
        }

        return true;
    }

    /**
     * Returns payment method image
     */
    public function getImage(): string
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : asset('../packages/Webkul/Iyzico/src/Resources/assets/images/iyzico.svg');
    }
}
