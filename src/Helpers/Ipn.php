<?php

namespace Webkul\Iyzico\Helpers;

use Webkul\Sales\Contracts\Order;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;

class Ipn
{
    /**
     * Ipn post data
     */
    protected array $post;

    /**
     * Order object
     */
    protected Order $order;

    /**
     * OrderRepository object
     */
    protected OrderRepository $orderRepository;

    /**
     * InvoiceRepository object
     */
    protected InvoiceRepository $invoiceRepository;

    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository
    ) {
        $this->orderRepository = $orderRepository;

        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * This function process the ipn sent from iyzico end
     */
    public function processIpn(array $post): void
    {
        $this->post = $post;

        if (! $this->postBack()) {
            return;
        }

        try {
            if (isset($this->post['txn_type']) && $this->post['txn_type'] == 'recurring_payment') {

            } else {
                $this->getOrder();

                $this->processOrder();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Load order via ipn invoice id
     */
    protected function getOrder(): void
    {
        if (empty($this->order)) {
            $this->order = $this->orderRepository->findOneByField(['cart_id' => $this->post['invoice']]);
        }
    }

    /**
     * Process order and create invoice
     */
    protected function processOrder(): void
    {
        if ($this->post['payment_status'] == 'Completed') {
            if ($this->post['mc_gross'] != $this->order->grand_total) {
                return;
            } else {
                $this->orderRepository->update(['status' => 'processing'], $this->order->id);

                if ($this->order->canInvoice()) {
                    $invoice = $this->invoiceRepository->create($this->prepareInvoiceData());
                }
            }
        }
    }

    /**
     * Prepares order's invoice data for creation
     */
    protected function prepareInvoiceData(): array
    {
        $invoiceData = [
            'order_id' => $this->order->id,
        ];

        foreach ($this->order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}
