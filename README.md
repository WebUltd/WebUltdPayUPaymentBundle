# WebUltdPayUPaymentBundle

![WebUltd Logo] (http://webultd.com/static/img/logo.png)

## Instalacja

1. Dodanie wpisu do pliku deps:
```
[WebUltdPayUPaymentBundle]
    git=https://github.com/WebUltd/WebUltdPayUPaymentBundle.git
    target=/bundles/webultd/Payu/PaymentBundle
```

2. Dodanie wpisu do app/autoload.php:
```php
$loader->registerNamespaces(array(
    ...
    'webultd' => __DIR__.'/../vendor/bundles',
    ...
));
```

3. Dodanie wpisu do app/AppKernel.php:
```php
$bundles = array(
    ...
    new webultd\Payu\PaymentBundle\webultdPayuPaymentBundle(),
);
```

4. Konfiguracja:
```
webultd_payu_payment:
    file: %kernel.root_dir%/../vendor/bundles/webultd/Payu/PaymentBundle/sdk/openpayu.php
    environment: sandbox
    merchant_pos_id: xxxx # tutaj wstawiamy pos_id dostępny w PayU
    pos_auth_key: xxxx # pos_auth_key z serwisu PayU
    client_id: xxxx # client_id z serwisu PayU
    client_secret: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx # client_secret z serwisu PayU
    signature_key: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx # signature_key z serwisu PayU
```
