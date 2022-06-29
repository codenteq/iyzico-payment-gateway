<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('iyzico::app.resources.title') }}</title>

    @if ($favicon = core()->getCurrentChannel()->favicon_url)
        <link rel="icon" sizes="16x16" href="{{ $favicon }}"/>
    @else
        <link rel="icon" sizes="16x16" href="{{ asset('/themes/velocity/assets/images/static/v-icon.png') }}"/>
    @endif
</head>
<body>
{!! $paymentForm !!}
<div id="iyzipay-checkout-form" class="responsive"></div>
</body>
</html>