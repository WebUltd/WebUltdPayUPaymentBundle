<?php

namespace webultd\Payu\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="order_requests")
 *
 */
class OrderRequest
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=32)
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Order")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order;

    /**
     * @ORM\Column(type="string", length=15)
     */
    protected $customerIp;

    /**
     * @ORM\Column(type="string", length=160)
     */
    protected $notifyUrl;

    /**
     * @ORM\Column(type="string", length=160)
     */
    protected $cancelUrl;

    /**
     * @ORM\Column(type="string", length=160)
     */
    protected $completeUrl;



    /**
     * Set id
     *
     * @param integer $id
     * @return OrderRequest
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customerIp
     *
     * @param string $customerIp
     * @return OrderRequest
     */
    public function setCustomerIp($customerIp)
    {
        $this->customerIp = $customerIp;
        return $this;
    }

    /**
     * Get customerIp
     *
     * @return string
     */
    public function getCustomerIp()
    {
        return $this->customerIp;
    }

    /**
     * Set notifyUrl
     *
     * @param string $notifyUrl
     * @return OrderRequest
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
        return $this;
    }

    /**
     * Get notifyUrl
     *
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    /**
     * Set cancelUrl
     *
     * @param string $cancelUrl
     * @return OrderRequest
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;
        return $this;
    }

    /**
     * Get cancelUrl
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * Set completeUurl
     *
     * @param string $completeUrl
     * @return OrderRequest
     */
    public function setCompleteUrl($completeUrl)
    {
        $this->completeUrl = $completeUrl;
        return $this;
    }

    /**
     * Get completeUurl
     *
     * @return string
     */
    public function getCompleteUrl()
    {
        return $this->completeUrl;
    }

    /**
     * Set order
     *
     * @param webultd\Payu\PaymentBundle\Entity\Order $order
     * @return OrderRequest
     */
    public function setOrder(\webultd\Payu\PaymentBundle\Entity\Order $order = null)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return webultd\Payu\PaymentBundle\Entity\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }
}