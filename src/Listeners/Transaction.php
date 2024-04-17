<?php

namespace Webkul\Iyzico\Listeners;

use Webkul\Iyzico\Payment\Iyzico;
use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Repositories\OrderTransactionRepository;

class Transaction
{
    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(
        protected Iyzico $iyzico,
        protected OrderTransactionRepository $orderTransactionRepository
    ) {
    }

    /**
     * Save the transaction data for online payment.
     *
     * @param  Invoice  $invoice
     * @return void
     */
    public function saveTransaction($invoice): void
    {
        $data = request()->all();

        if ($invoice->order->payment->method == 'iyzico') {
            if (isset($data['orderData']['orderID'])) {
                $transactionDetails = $this->iyzico->getOrder($data['orderData']['orderID']);

                $transactionDetails = json_decode(json_encode($transactionDetails), true);

                if ($transactionDetails['statusCode'] == 200) {
                    $this->orderTransactionRepository->create([
                        'transaction_id' => $transactionDetails['result']['id'],
                        'status'         => $transactionDetails['result']['status'],
                        'type'           => $transactionDetails['result']['intent'],
                        'amount'         => $transactionDetails['result']['purchase_units'][0]['amount']['value'],
                        'payment_method' => $invoice->order->payment->method,
                        'order_id'       => $invoice->order->id,
                        'invoice_id'     => $invoice->id,
                        'data'           => json_encode(
                            array_merge(
                                $transactionDetails['result']['purchase_units'],
                                $transactionDetails['result']['payer']
                            )
                        ),
                    ]);
                }
            }
        } elseif ($invoice->order->payment->method == 'iyzico') {
            $this->orderTransactionRepository->create([
                'transaction_id' => $data['txn_id'],
                'status'         => $data['payment_status'],
                'type'           => $data['payment_type'],
                'payment_method' => $invoice->order->payment->method,
                'order_id'       => $invoice->order->id,
                'invoice_id'     => $invoice->id,
                'data'           => json_encode($data),
            ]);
        }
    }
}
