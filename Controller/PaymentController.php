<?php

namespace webultd\Payu\PaymentBundle\Controller;

use webultd\Payu\PaymentBundle\Entity\OrderItem;

use webultd\Payu\PaymentBundle\Entity\OrderRequest;

use webultd\Payu\PaymentBundle\Entity\Order;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use webultd\Payu\PaymentBundle;


class PaymentController extends Controller
{
    public function orderSummaryAction()
    {
        $paymentApi = $this->get('webultd_payu_payment.api');
        $sessionId = $paymentApi->generateSessionId();

        $shoppingCart = $this->get('webultd_payu_payment.shopping_cart');
        //this causes unkown method error so added hardoced version for you to fix
        // $applicationId = $shoppingCart->getApplicationId();
        $applicationId = "some id";

        $entityManager = $this->get('doctrine')->getEntityManager();

        $items = $shoppingCart->getItems();

        $entityManager->getConnection()->beginTransaction();

        $order = new Order();
        $order->setSessionId($sessionId);
        $order->setApplicationId($applicationId);
        $order->setType($paymentApi->getOrderType());
        $order->setAmountNet($shoppingCart->getAmountNet() / 100);
        $order->setAmountGross($shoppingCart->getAmountGross() / 100);
        $order->setTax($shoppingCart->getTax());
        $order->setCreatedAt(new \DateTime());
        $order->setStatus('STATUS'); //TODO

        $entityManager->persist($order);
        $entityManager->flush();

        $requestData = array(
            'grand_total' => $shoppingCart->getGrandTotal(),
            'currency_code' => $paymentApi->getCurrencyCode(),
            'order_type' => $paymentApi->getOrderType(),
            'items' => $items,
            'order_id' => $order->getId(),
        );

        $request = $paymentApi->createRequest($requestData);

        if(!$request->getSuccess()) {
            $entityManager->getConnection()->rollback();
            return $this->render('webultdPayuPaymentBundle:Payment:order_summary.html.twig', array('createRequestSuccess' => false));
        }

        try {
            $orderRequest = new OrderRequest();
            $request = $request->getRequest();

            $orderRequest->setId($request['ReqId']);
            $orderRequest->setCustomerIp($request['CustomerIp']);
            $orderRequest->setNotifyUrl($request['NotifyUrl']);
            $orderRequest->setCompleteUrl($request['OrderCompleteUrl']);
            $orderRequest->setCancelUrl($request['OrderCancelUrl']);
            $orderRequest->setOrder($order);

            $orderItems = array();
            foreach($items as $item) {
                $product = $entityManager->find('webultd\Payu\PaymentBundle\Entity\Product', $item['ShoppingCartItem']['product_id']);

                $orderItem = new OrderItem();
                $orderItem->setName($product->getName());
                $orderItem->setOrder($order);
                $orderItem->setPrice($product->getPrice());
                $orderItem->setProduct($product);
                $orderItem->setQuantity($item['ShoppingCartItem']['Quantity']);
                $orderItems[] = $orderItem;

                $entityManager->persist($orderItem);
            }

            $entityManager->persist($orderRequest);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
        } catch(\Exception $err) {
            $entityManager->getConnection()->rollback();
            $entityManager->close();
            return $this->render('webultdPayuPaymentBundle:Payment:order_summary.html.twig', array('createRequestSuccess' => false));
        }

        return $this->render('webultdPayuPaymentBundle:Payment:order_summary.html.twig', array('createRequestSuccess' => true, 'order' => $order, 'orderItems' => $orderItems));
    }

    public function authorizedAction(Request $request)
    {
        $paymentApi = $this->get('webultd_payu_payment.api');
        $router = $this->get('router');
        $code = $request->query->getAlnum('code');

        $result = $paymentApi->getAccessTokenByCode($code, $router->generate('webultdPayuPaymentBundle_authorized', array(), true));

        if($result->getSuccess()) {
            return $this->redirect($paymentApi->getSummaryUrl() . '?sessionId=' .  urlencode($paymentApi->getSessionId()) . '&oauth_token=' . urlencode($result->getAccessToken()));
        } else {
            var_dump($result); die; // TODO
        }

        return $this->render('webultdPayuPaymentBundle:Payment:authorized.html.twig', array('summaryUrl' => $paymentApi->getSummaryUrl(), 'accessToken' => $result->getAccessToken()));
    }

    public function successAction()
    {
        $shoppingCart = $this->get('webultd_payu_payment.shopping_cart');
        $shoppingCart->clear();
        return $this->render('webultdPayuPaymentBundle:Payment:success.html.twig');
    }

    public function cancelAction()
    {
        return $this->render('webultdPayuPaymentBundle:Payment:cancel.html.twig');
    }

    public function statusAction()
    {
        $paymentApi = $this->get('webultd_payu_payment.api');

        if(!isset($_REQUEST['document'])) {
            file_put_contents("/home/webultd1/debug.txt", var_export($_REQUEST, true));
            throw $this->createNotFoundException();
        }

        try {
            $result = $paymentApi->retrieveOrder($_REQUEST['document']);
            file_put_contents("/home/webultd1/debug.txt", var_export($result, true));
        } catch (Exception $e) {
            file_put_contents("/home/webultd1/debug.txt", $e->getMessage());
        }

        return $this->render('webultdPayuPaymentBundle:Payment:cancel.html.twig');
    }
}
