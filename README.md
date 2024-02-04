# Iyzico Payment Gateway
[![License](https://poser.pugx.org/codenteq/iyzico-payment-gateway/license)](https://github.com/codenteq/iyzico-payment-gateway/blob/master/LICENSE)
<a href="https://packagist.org/packages/codenteq/iyzico-payment-gateway"><img src="https://poser.pugx.org/codenteq/iyzico-payment-gateway/d/total" alt="Total Downloads"></a>

## 1. Introduction:

Install this package now to receive secure payments in your online store. Iyzico offers an easy and secure payment gateway

## 2. Requirements:

* **PHP**: 8.0 or higher.
* **Bagisto**: v2.0.*
* **Composer**: 1.6.5 or higher.

## 3. Installation:

- Run the following command
```
composer require codenteq/iyzico-payment-gateway
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
Webkul\Iyzico\Providers\IyzicoServiceProvider::class,
```

- Goto composer.json file and add following line under 'psr-4'

```
"Webkul\\Iyzico\\": "packages/Webkul/Iyzico/src"
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
