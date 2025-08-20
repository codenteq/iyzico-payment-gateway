<?php

namespace Webkul\Iyzico\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\CheckoutForm;
use Iyzipay\Model\CheckoutFormInitialize;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use Iyzipay\Request\RetrieveCheckoutFormRequest;
use Webkul\Checkout\Facades\Cart;
use Webkul\Customer\Models\Customer;
use Webkul\Iyzico\Helpers\Ipn;
use Webkul\Iyzico\Helpers\IyzicoApi;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
        protected Ipn $ipnHelper
    ) {
        //
    }

    /**
     * Redirects to the Iyzico server.
     *
     * \Illuminate\Contracts\View\View
     * \Illuminate\Foundation\Application
     * \Illuminate\Contracts\View\Factory
     * \Illuminate\Contracts\Foundation\Application
     */
    public function redirect(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $cart = Cart::getCart();
        if (!$cart || !$cart->billing_address) {
            return redirect()->route('shop.checkout.cart.index')->with('error', 'Billing address is required.');
        }

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('shop.checkout.cart.index')->with('error', 'User not authenticated.');
        }

        $requestIyzico = new CreateCheckoutFormInitializeRequest();
        $requestIyzico->setLocale(app()->getLocale());
        $requestIyzico->setPrice($cart['sub_total']);
        $requestIyzico->setPaidPrice($cart['grand_total']);
        $requestIyzico->setCurrency($cart['cart_currency_code']);
        $requestIyzico->setBasketId($cart['id']);
        $requestIyzico->setPaymentGroup(PaymentGroup::PRODUCT);
        $requestIyzico->setCallbackUrl(route('iyzico.callback'));
        $requestIyzico->setEnabledInstallments([1, 2, 3, 6, 9]);

        $buyer = new Buyer();
        $buyer->setId($cart['id']);
        $buyer->setName($cart['customer_first_name']);
        $buyer->setSurname($cart['customer_last_name']);
        $buyer->setGsmNumber($user['phone']);
        $buyer->setEmail($user['email']);
        $buyer->setIdentityNumber(11111111111);
        $buyer->setLastLoginDate((string) $cart['created_at']);
        $buyer->setRegistrationDate((string) $user['created_at']);
        $buyer->setRegistrationAddress($cart->billing_address['address']);
        $buyer->setIp($request->ip());
        $buyer->setCity($cart->billing_address['city']);
        $buyer->setCountry($cart->billing_address['country']);
        $buyer->setZipCode($cart->billing_address['postcode']);

        $requestIyzico->setBuyer($buyer);
        $shippingAddress = new Address();
        $shippingAddress->setContactName($cart['customer_first_name'].' '.$cart['customer_last_name']);
        $shippingAddress->setCity($cart->billing_address['city']);
        $shippingAddress->setCountry($cart->billing_address['country']);
        $shippingAddress->setAddress($cart->billing_address['address']);
        $shippingAddress->setZipCode($cart->billing_address['postcode']);
        $requestIyzico->setShippingAddress($shippingAddress);

        $billingAddress = new Address();
        $billingAddress->setContactName($cart->customer_first_name.' '.$cart->customer_last_name);
        $billingAddress->setCity($cart->billing_address['city']);
        $billingAddress->setCountry($cart->billing_address['country']);
        $billingAddress->setAddress($cart->billing_address['address']);
        $billingAddress->setZipCode($cart->billing_address['postcode']);
        $requestIyzico->setBillingAddress($billingAddress);

        $basketItems = [];
        $products = 0;
        foreach ($cart['items'] as $product) {
            $BasketItem = new BasketItem();
            $BasketItem->setId($product['id']);
            $BasketItem->setName($product['name']);
            $BasketItem->setCategory1($product->getTypeInstance()->isStockable() ? 'PHYSICAL_GOODS' : 'DIGITAL_GOODS');
            $BasketItem->setCategory2($product->getTypeInstance()->isStockable() ? 'PHYSICAL_GOODS' : 'DIGITAL_GOODS');
            $BasketItem->setItemType(BasketItemType::PHYSICAL);
            $BasketItem->setPrice($product['total']);
            $basketItems[$products] = $BasketItem;
            $products++;
        }
        $requestIyzico->setBasketItems($basketItems);

        $checkoutFormInitialize = CheckoutFormInitialize::create($requestIyzico, IyzicoApi::options());
        $paymentForm = $checkoutFormInitialize->getCheckoutFormContent();
        $paymentPageUrl = $checkoutFormInitialize->getPaymentPageUrl().'&iframe=true';
        $checkoutFormInitialize->setPaymentPageUrl($paymentPageUrl);

        return view('iyzico::iyzico-form', compact('paymentForm'));
    }

    /**
     * Redirects to the Iyzico server.
     */
    public function callback(Request $request): RedirectResponse
    {
        $requestIyzico = new RetrieveCheckoutFormRequest();
        $requestIyzico->setLocale(app()->getLocale());
        $requestIyzico->setToken($request->token);
        $checkoutForm = CheckoutForm::retrieve($requestIyzico, IyzicoApi::options());

        if ($checkoutForm->getPaymentStatus() == 'SUCCESS') {
            $paymentTransactionId = $checkoutForm->getPaymentItems()[0]->getPaymentTransactionId();

            if (! is_null($paymentTransactionId)) {
                session(['payment_transaction_id' => $paymentTransactionId]);
            }

            return redirect()->route('iyzico.success');
        } else {
            return redirect()->route('iyzico.cancel');
        }
    }

    /**
     * Place an order and redirect to the success page.
     *
     * @throws \Exception
     */
    public function success(): RedirectResponse
    {
        $cart = Cart::getCart();

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        $this->savePaymentTransactionId($order['id']);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
    /**
     * Redirect to the cart page with error message.
     */
    public function failure(): RedirectResponse
    {
        session()->flash('error', 'Iyzico payment was either cancelled or the transaction failed.');

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Prepares order's invoice data for creation.
     */
    protected function prepareInvoiceData($order): array
    {
        $invoiceData = [
            'order_id' => $order->id,
            'invoice'  => ['items' => []],
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    /**
     * Saves the payment transaction ID to the database.
     */
    protected function savePaymentTransactionId(int $orderId): void
    {
        OrderPayment::where('order_id', $orderId)->update(['additional' => session('payment_transaction_id')]);
    }
}
