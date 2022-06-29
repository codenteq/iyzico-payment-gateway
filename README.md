# Iyzico Payment Gateway
[![License](https://poser.pugx.org/bagisto/bagisto-gdpr/license)](https://github.com/ahmetarsiv/iyzico-payment-gateway/blob/master/LICENSE)

## 1. Introduction:

Install this package now to receive secure payments in your online store. Iyzico offers an easy and secure payment gateway

## 2. Requirements:

* **PHP**: 7.3 or higher.
* **Bagisto**: v1.4.*
* **Composer**: 1.6.5 or higher.

## 3. Installation:

Create packages/Webkul/IyzicoPayment/ folders then follow the steps below
~~~
composer require iyzico/iyzipay-php
~~~

> Open ‘app.php’ file inside ‘config’ folder & add your service provider inside the ‘providers’ array.
~~~
Webkul\IyzicoPayment\Providers\IyzicoPaymentServiceProvider::class,
~~~

> Goto ‘composer.json’ file and add following line under psr-4
~~~
"Webkul\\IyzicoPayment\\": "packages/Webkul/IyzicoPayment/src"
~~~

~~~
composer dump-autoload
~~~

> Copy the callback code to app/Http/Middlware/VerifyCsrfToken.php protected
~~~
'iyzico-callback'
~~~

~~~
php artisan route:cache
~~~

~~~
composer dump-autoload
~~~

### License
Iyzico payment gateway is always safe! [MIT License](https://github.com/Arsivpro/iyzico-payment-gateway/blob/main/LICENSE).

Thank you to all our backers! 🙏

<a href="https://opencollective.com/arsivpro#contributors" target="_blank"><img src="https://opencollective.com/arsivpro/backers.svg?width=890"></a>
