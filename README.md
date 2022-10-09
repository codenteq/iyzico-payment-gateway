# Iyzico Payment Gateway
[![License](https://poser.pugx.org/ahmetarsiv/iyzico-payment-gateway/license)](https://github.com/ahmetarsiv/iyzico-payment-gateway/blob/master/LICENSE)
<a href="https://packagist.org/packages/ahmetarsiv/iyzico-payment-gateway"><img src="https://poser.pugx.org/ahmetarsiv/iyzico-payment-gateway/d/total.svg" alt="Total Downloads"></a>

## 1. Introduction:

Install this package now to receive secure payments in your online store. Iyzico offers an easy and secure payment gateway

## 2. Requirements:

* **PHP**: 8.0 or higher.
* **Bagisto**: v1.4.*
* **Composer**: 1.6.5 or higher.

## 3. Installation:

- Run the following command
```
composer require ahmetarsiv/iyzico-payment-gateway
```

- Run these commands below to complete the setup
```
composer dump-autoload
```

> WARNING <br>
> It will check existence of the .env file, if it exists then please update the file manually with the below details.
```
IYZICO_API_KEY
IYZICO_SECRET_KEY
IYZICO_BASE_URL
```

- Run these commands below to complete the setup
```
php artisan optimize
```

> Copy the callback code to app/Http/Middlware/VerifyCsrfToken.php protected
~~~
'iyzico-callback'
~~~

## Installation without composer:

- Unzip the respective extension zip and then merge "packages" and "storage" folders into project root directory.
- Goto config/app.php file and add following line under 'providers'

```
Webkul\IyzicoPayment\Providers\IyzicoPaymentServiceProvider::class,
```

- Goto composer.json file and add following line under 'psr-4'

```
"Webkul\\IyzicoPayment\\": "packages/Webkul/IyzicoPayment/src"
```

- Run these commands below to complete the setup

```
composer dump-autoload
```

> WARNING <br>
> It will check existence of the .env file, if it exists then please update the file manually with the below details.
```
IYZICO_API_KEY
IYZICO_SECRET_KEY
IYZICO_BASE_URL
```

```
php artisan optimize
```

> Copy the callback code to app/Http/Middlware/VerifyCsrfToken.php protected
~~~
'iyzico-callback'
~~~

> That's it, now just execute the project on your specified domain.

## Contributors

This project is on [Open Collective](https://opencollective.com/arsivpro), and it exists thanks to the people who contribute.

<a href="https://github.com/ahmetarsiv/iyzico-payment-gateway/graphs/contributors"><img src="https://opencollective.com/arsivpro/backers.svg?width=890"/></a>
