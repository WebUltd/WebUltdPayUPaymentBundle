# WebUltdPayUPaymentBundle

![WebUltd Logo](http://webultd.com/static/img/logo.png)

## Licencja

MIT (X11)

OPROGRAMOWANIE JEST DOSTARCZONE TAKIM, JAKIE JEST, BEZ JAKIEJKOLWIEK GWARANCJI,
WYRAŹNEJ LUB DOROZUMIANEJ, NIE WYŁĄCZAJĄC GWARANCJI PRZYDATNOŚCI HANDLOWEJ LUB
PRZYDATNOŚCI DO OKREŚLONYCH CELÓW A TAKŻE BRAKU WAD PRAWNYCH. W ŻADNYM
PRZYPADKU TWÓRCA LUB POSIADACZ PRAW AUTORSKICH NIE MOŻE PONOSIĆ
ODPOWIEDZIALNOŚCI Z TYTUŁU ROSZCZEŃ LUB WYRZĄDZONEJ SZKODY A TAKŻE ŻADNEJ INNEJ
ODPOWIEDZIALNOŚCI CZY TO WYNIKAJĄCEJ Z UMOWY, DELIKTU, CZY JAKIEJKOLWIEK INNEJ
PODSTAWY POWSTAŁEJ W ZWIĄZKU Z OPROGRAMOWANIEM LUB UŻYTKOWANIEM GO LUB
WPROWADZANIEM GO DO OBROTU.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

## Diagram wywołań akcji
![Action Flow Diagram](http://webultd.com/static/img/webultd_payu_payment_flow.png)

## Instalacja

### Dodanie wpisu do pliku deps:

```
[WebUltdPayUPaymentBundle]
    git=https://github.com/WebUltd/WebUltdPayUPaymentBundle.git
    target=/bundles/webultd/Payu/PaymentBundle
```
### Dodanie wpisu do app/autoload.php:

```php
$loader->registerNamespaces(array(
    ...
    'webultd' => __DIR__.'/../vendor/bundles',
    ...
));
```

### Dodanie wpisu do app/AppKernel.php:

```php
$bundles = array(
    ...
    new webultd\Payu\PaymentBundle\webultdPayuPaymentBundle(),
);
```

### Routing (app/config/routing.yml):

```
webultdPayuPaymentBundle:
    resource: "@webultdPayuPaymentBundle/Resources/config/routing.yml"
    prefix:   /payment
```

## Konfiguracja

```
webultd_payu_payment:
    file: %kernel.root_dir%/../vendor/bundles/webultd/Payu/PaymentBundle/sdk/openpayu.php
    environment: sandbox # dla środowiska produkcyjnego zmieniamy na "secure"
    merchant_pos_id: xxxx # tutaj wstawiamy pos_id dostępny w PayU
    pos_auth_key: xxxx # pos_auth_key z serwisu PayU
    client_id: xxxx # client_id z serwisu PayU
    client_secret: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx # client_secret z serwisu PayU
    signature_key: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx # signature_key z serwisu PayU
    shopping_cart:
        tax: 23 # wartość podatku, domyślnie 23(również gdy nie podano)
```

## Akcje

```
// webultd/PayU/PaymentBundle/Resources/config/routing.yml
webultdPayuPaymentBundle_order_summary: # akcja podsumowania zamówienia oraz możliwość dokonania płatności
    pattern:  /summary
    defaults: { _controller: webultdPayuPaymentBundle:Payment:orderSummary }

webultdPayuPaymentBundle_authorized: # akcja wywoływana po poprawnej autoryzacji w PayU
    pattern:  /authorized
    defaults: { _controller: webultdPayuPaymentBundle:Payment:authorized }

webultdPayuPaymentBundle_success: # akcja po poprawnym dokonaniu płatności
    pattern:  /success
    defaults: { _controller: webultdPayuPaymentBundle:Payment:success }

webultdPayuPaymentBundle_cancel: # akcja po anulowaniu płatności
    pattern:  /cancel
    defaults: { _controller: webultdPayuPaymentBundle:Payment:cancel }

webultdPayuPaymentBundle_status: # akcja nasłuchująca notyfikacje z PayU
    pattern:  /status
    defaults: { _controller: webultdPayuPaymentBundle:Payment:status }
    requirements:
        _method: POST
```
