<?php

namespace Webkul\Iyzico\Listeners;

use Iyzipay\Model\Refund as RefundModel;
use Iyzipay\Request\CreateRefundRequest;
use Webkul\Admin\Listeners\Base;
use Webkul\Admin\Mail\Order\RefundedNotification;
use Webkul\Iyzico\Helpers\IyzicoApi;

class Refund extends Base
{
    /**
     * After order is created
     */
    public function afterCreated(\Webkul\Sales\Contracts\Refund $refund): void
    {
        $this->refundOrder($refund);

        try {
            if (! core()->getConfigData('emails.general.notifications.emails.general.notifications.new_refund')) {
                return;
            }

            $this->prepareMail($refund, new RefundedNotification($refund));
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * After Refund is created
     */
    public function refundOrder(\Webkul\Sales\Contracts\Refund $refund): void
    {
        $order = $refund->order;

        if ($order->payment->method === 'iyzico') {
            if ($refund->total_qty > 0) {
                $request = new CreateRefundRequest();
                $request->setIp(request()->ip());
                $request->setPrice($refund->grand_total);
                $request->setPaymentTransactionId($order->payment['additional']);
                $request->setLocale(app()->getLocale());
                $request->setCurrency($refund->order_currency_code);

                RefundModel::create($request, IyzicoApi::options());
            }
        }
    }
}