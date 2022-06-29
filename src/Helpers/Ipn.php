<?php


namespace Webkul\IyzicoPayment\Helpers;

use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;

class Ipn
{
    /**
     * Ipn post data
     *
     * @var array
     */
    protected $post;

    /**
     * Order object
     *
     * @var \Webkul\Sales\Contracts\Order
     */
    protected $order;

    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * InvoiceRepository object
     *
     * @var \Webkul\Sales\Repositories\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Create a new helper instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\InvoiceRepository  $invoiceRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository
    )
    {
        $this->orderRepository = $orderRepository;

        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * This function process the ipn sent from iyzico end
     *
     * @param  array  $post
     * @return null|void|\Exception
     */
    public function processIpn($post)
    {
        $this->post = $post;

        if (! $this->postBack()) {
            return;
        }

        try {
            if (isset($this->post['txn_type']) && 'recurring_payment' == $this->post['txn_type']) {

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
     *
     * @return void
     */
    protected function getOrder()
    {
        if (empty($this->order)) {
            $this->order = $this->orderRepository->findOneByField(['cart_id' => $this->post['invoice']]);
        }
    }

    /**
     * Process order and create invoice
     *
     * @return void
     */
    protected function processOrder()
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
     *
     * @return array
     */
    protected function prepareInvoiceData()
    {
        $invoiceData = [
            "order_id" => $this->order->id,
        ];

        foreach ($this->order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}