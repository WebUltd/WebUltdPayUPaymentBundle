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
    environment: sandbox # dla środowiska produkcyjnego zmieniamy na "secure"
    merchant_pos_id: xxxx # tutaj wstawiamy pos_id dostępny w PayU
    pos_auth_key: xxxx # pos_auth_key z serwisu PayU
    client_id: xxxx # client_id z serwisu PayU
    client_secret: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx # client_secret z serwisu PayU
    signature_key: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx # signature_key z serwisu PayU
```

5. Routing:
```
webultdPayuPaymentBundle:
    resource: "@webultdPayuPaymentBundle/Resources/config/routing.yml"
    prefix:   /payment
```

6. Akcje:
```
// webultd/PayU/PaymentBundle/Resources/config/routing.yml
# akcja podsumowania zamówienia oraz możliwość dokonania płatności
webultdPayuPaymentBundle_order_summary:
    pattern:  /summary
    defaults: { _controller: webultdPayuPaymentBundle:Payment:orderSummary }

# akcja wywoływana po poprawnej autoryzacji w PayU
webultdPayuPaymentBundle_authorized:
    pattern:  /authorized
    defaults: { _controller: webultdPayuPaymentBundle:Payment:authorized }

# akcja po poprawnym dokonaniu płatności
webultdPayuPaymentBundle_success:
    pattern:  /success
    defaults: { _controller: webultdPayuPaymentBundle:Payment:success }

# akcja po anulowaniu płatności
webultdPayuPaymentBundle_cancel:
    pattern:  /cancel
    defaults: { _controller: webultdPayuPaymentBundle:Payment:cancel }

# akcja nasłuchująca notyfikacje z PayU
webultdPayuPaymentBundle_status:
    pattern:  /status
    defaults: { _controller: webultdPayuPaymentBundle:Payment:status }
    requirements:
        _method: POST
```
