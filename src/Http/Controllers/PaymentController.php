<?php

namespace Webkul\Iyzico\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;
use Webkul\Customer\Models\Customer;
use Webkul\Iyzico\Helpers\IycizoApi;
use Webkul\Iyzico\Helpers\IyzicoApi;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Iyzico\Helpers\Ipn;
use Webkul\Shop\Http\Controllers\Controller;

class PaymentController
{
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * Ipn object
     *
     * @var \Webkul\Iyzico\Helpers\Ipn
     */
    protected $ipnHelper;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Attribute\Repositories\OrderRepository $orderRepository
     * @param \Webkul\Iyzico\Helpers\Ipn $ipnHelper
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        Ipn             $ipnHelper
    )
    {
        $this->orderRepository = $orderRepository;

        $this->ipnHelper = $ipnHelper;
    }

    public function redirect(Request $request)
    {
        $cart = Cart::getCart();
        $address = $cart->billing_address;
        $user = Customer::find($cart->customer_id);

        $requestIyzico = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $requestIyzico->setLocale(app()->getLocale());
        $requestIyzico->setConversationId(rand());
        $requestIyzico->setPrice(number_format($cart['base_sub_total'], '2', '.', ''));
        $requestIyzico->setPaidPrice(number_format($cart['base_grand_total'], '2', '.', ''));
        $requestIyzico->setCurrency($cart['cart_currency_code']);
        $requestIyzico->setBasketId("B67832");
        $requestIyzico->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $requestIyzico->setCallbackUrl(route('iyzico.callback'));
        $requestIyzico->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId($cart->id);
        $buyer->setName($cart->customer_first_name);
        $buyer->setSurname($cart->customer_last_name);
        $buyer->setGsmNumber($address->phone);
        $buyer->setEmail($address->email);
        $buyer->setIdentityNumber(rand());
        $buyer->setLastLoginDate((string)$cart->created_at);
        $buyer->setRegistrationDate((string)$user->created_at);
        $buyer->setRegistrationAddress($address->address1);
        $buyer->setIp($request->ip());
        $buyer->setCity($address->state);
        $buyer->setCountry("TR");
        $buyer->setZipCode($address->postcode);

        $requestIyzico->setBuyer($buyer);
        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($cart->customer_first_name . ' ' . $cart->customer_last_name);
        $shippingAddress->setCity($address->state);
        $shippingAddress->setCountry($address->city);
        $shippingAddress->setAddress($address->address1);
        $shippingAddress->setZipCode($address->postcode);
        $requestIyzico->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($cart->customer_first_name . ' ' . $cart->customer_last_name);
        $billingAddress->setCity($address->state);
        $billingAddress->setCountry($address->city);
        $billingAddress->setAddress($address->address1);
        $billingAddress->setZipCode($address->postcode);
        $requestIyzico->setBillingAddress($billingAddress);


        $basketItems = array();
        $products = 0;
        foreach ($cart['items'] as $product) {
            $BasketItem = new \Iyzipay\Model\BasketItem();
            $BasketItem->setId($product->id);
            $BasketItem->setName($product->name);
            $BasketItem->setCategory1("Teknoloji");
            $BasketItem->setCategory2("Bilgisayar");
            $BasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
            $BasketItem->setPrice(number_format($product['total'], '2', '.', ''));
            $basketItems[$products] = $BasketItem;
            $products++;
        }
        $requestIyzico->setBasketItems($basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($requestIyzico, IyzicoApi::options());
        $paymentForm = $checkoutFormInitialize->getCheckoutFormContent();

        return view('iyzico::iyzico-form', compact('paymentForm'));
    }

    public function callback(Request $request)
    {
        $requestIyzico = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $requestIyzico->setLocale(app()->getLocale());
        $requestIyzico->setToken($request->token);
        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($requestIyzico, IyzicoApi::options());

        if ($checkoutForm->getPaymentStatus() == 'SUCCESS') {
            return redirect()->route('iyzico.success');
        } else {
            return redirect('/checkout/onepage');
        }
    }

    public function success()
    {
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        session()->flash('order', $order);

        return redirect()->route('shop.checkout.success');
    }
}
