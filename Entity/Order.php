<?php

namespace webultd\Payu\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="orders")
 *
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(name="session_id", type="string", length=32, unique=true)
     */
    protected $sessionId;

    /**
     * @ORM\Column(name="application_id", type="integer")
     */
    protected $applicationId;

    /**
     * @ORM\Column(type="string", length=8)
     */
    protected $type;

    /**
     * @ORM\Column(name="amount_net", type="decimal", precision=6, scale=2)
     */
    protected $amountNet;

    /**
     * @ORM\Column(name="amount_gross", type="decimal", precision=6, scale=2)
     */
    protected $amountGross;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $tax;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="paid_at", type="datetime", nullable=true)
     */
    protected $paidAt;

    /**
     * @ORM\Column(name="status", type="string", length=60)
     */
    protected $status;


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
     * Set sessionId
     *
     * @param string $sessionId
     * @return Order
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set applicationId
     *
     * @param string $applicationId
     * @return Order
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
        return $this;
    }

    /**
     * Get applicationId
     *
     * @return string
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Order
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set amountNet
     *
     * @param decimal $amountNet
     * @return Order
     */
    public function setAmountNet($amountNet)
    {
        $this->amountNet = $amountNet;
        return $this;
    }

    /**
     * Get amountNet
     *
     * @return decimal
     */
    public function getAmountNet()
    {
        return $this->amountNet;
    }

    /**
     * Set amountGross
     *
     * @param decimal $amountGross
     * @return Order
     */
    public function setAmountGross($amountGross)
    {
        $this->amountGross = $amountGross;
        return $this;
    }

    /**
     * Get amountGross
     *
     * @return decimal
     */
    public function getAmountGross()
    {
        return $this->amountGross;
    }

    /**
     * Set tax
     *
     * @param smallint $tax
     * @return Order
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * Get tax
     *
     * @return smallint
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     * @return Order
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set paidAt
     *
     * @param datetime $paidAt
     * @return Order
     */
    public function setPaidAt($paidAt)
    {
        $this->paidAt = $paidAt;
        return $this;
    }

    /**
     * Get paidAt
     *
     * @return datetime
     */
    public function getPaidAt()
    {
        return $this->paidAt;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set request
     *
     * @param webultd\Payu\PaymentBundle\Entity\OrderRequest $request
     * @return Order
     */
    public function setRequest(\webultd\Payu\PaymentBundle\Entity\OrderRequest $request = null)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get request
     *
     * @return webultd\Payu\PaymentBundle\Entity\OrderRequest
     */
    public function getRequest()
    {
        return $this->request;
    }
}