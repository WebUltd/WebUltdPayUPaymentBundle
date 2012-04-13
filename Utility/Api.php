<?php

namespace  webultd\Payu\PaymentBundle\Utility;

use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Session;
class Api
{
    const ORDER_TYPE_VIRTUAL = 'VIRTUAL';
    const ORDER_TYPE_MATERIAL = 'MATERIAL';

    private $session;
    private $sessionId;
    private $merchantPosId;
    private $posAuthKey;
    private $clientId;
    private $clientSecret;
    private $signatureKey;
    private $environment;
    private $currencyCode;
    private $orderType;

    private $authUrl;
    private $summaryUrl;

    public function __construct(Session $session, Router $router, $environement, $merchant_pos_id, $pos_auth_key, $client_id, $client_secret, $signature_key)
    {
        $this->session = $session;
        $this->session->start();

        $this->environment = $environement;
        $this->merchantPosId = $merchant_pos_id;
        $this->posAuthKey = $pos_auth_key;
        $this->clientId = $client_id;
        $this->clientSecret = $client_secret;
        $this->signatureKey = $signature_key;
        $this->currencyCode = 'PLN'; // TODO: from configuration
        $this->orderType = self::ORDER_TYPE_VIRTUAL;

        \OpenPayU_Configuration::environment($this->environment);
        \OpenPayU_Configuration::merchantPosId($this->merchantPosId);
        \OpenPayU_Configuration::posAuthKey($this->posAuthKey);
        \OpenPayU_Configuration::clientId($this->clientId);
        \OpenPayU_Configuration::clientSecret($this->clientSecret);
        \OpenPayU_Configuration::signatureKey($this->signatureKey);

        $this->authUrl = \OpenPayU_Configuration::$authUrl;
        $this->summaryUrl = \OpenPayU_Configuration::$summaryUrl;
        $this->notifyUrl = $router->generate('webultdPayuPaymentBundle_status', array(), true);
        $this->cancelUrl = $router->generate('webultdPayuPaymentBundle_cancel', array(), true);
        $this->completeUrl = $router->generate('webultdPayuPaymentBundle_success', array(), true);
    }

    public function createRequest(array $data)
    {

        $shoppingCart = array(
            'GrandTotal' => $data['grand_total'],
            'CurrencyCode' => $data['currency_code'],
            'ShoppingCartItems' => $data['items'],
        );

        $order = array (
            'MerchantPosId' => $this->merchantPosId,
            'SessionId' => $this->sessionId,
            'OrderUrl' => '/payment/order/id',
            'OrderCreateDate' => date("c"),
            'OrderDescription' => 'MASP Payment',
            'MerchantAuthorizationKey' => $this->posAuthKey,
            'OrderType' => $data['order_type'],
            'ShoppingCart' => $shoppingCart,
        );

        $OCReq = array (
            'ReqId' =>  md5(rand()),
            'CustomerIp' => $_SERVER['REMOTE_ADDR'],
            'NotifyUrl' => $this->notifyUrl,
            'OrderCancelUrl' => $this->cancelUrl,
            'OrderCompleteUrl' => $this->completeUrl,
            'Order' => $order,
            'RefOrderId' => $data['order_id'],
        );

        $result = \OpenPayU_Order::create($OCReq);

        return $result;
    }

    public function retrieveOrder($document)
    {
        $result = \OpenPayU_Order::consumeMessage($document);
        if ($result->message == 'OrderNotifyRequest') {
            return \OpenPayU_Order::retrieve($result->sessionId);
        }

        return null;
    }

    public function getSummaryUrl()
    {
        return $this->summaryUrl;
    }

    public function getAccessTokenByCode($code, $returnUri)
    {
        return \OpenPayU_OAuth::accessTokenByCode($code, $returnUri);
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getAuthUrl()
    {
        return $this->authUrl;
    }

    public function getSessionId()
    {
        return $this->session->get('payu_session_id');
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function getOrderType()
    {
        return $this->orderType;
    }

    public function generateSessionId()
    {
        $payuSessionId = md5(microtime() . rand());
        $this->sessionId = $payuSessionId;
        $this->session->set('payu_session_id', $this->sessionId);

        return $this->sessionId;
    }
}