<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace webultd\Payu\PaymentBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PayuExtension extends \Twig_Extension
{
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return array(
            'payu_button' => new \Twig_Function_Method($this, 'renderPayuButton', array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'payu';
    }

    public function renderPayuButton()
    {
        $paymentApi = $this->container->get('webultd_payu_payment.api');
        $templating = $this->container->get('templating');
        $session = $this->container->get('session');

        return $templating->render('webultdPayuPaymentBundle::paymentButton.html.twig', array(
            'authUrl' => $paymentApi->getAuthUrl(),
            'redirectUri' => 'generated_url',
            'response_type' => 'code',
            'clientId' => $paymentApi->getClientId(),
        ));
    }
}
