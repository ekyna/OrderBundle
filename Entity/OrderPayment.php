<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Ekyna\Bundle\PaymentBundle\Entity\Payment;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderPaymentInterface;

/**
 * Class OrderPayment
 * @package Ekyna\Bundle\OrderBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderPayment extends Payment implements OrderPaymentInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Ekyna\Bundle\orderBundle\Entity\Order
     */
    protected $order;


    /**
     * Returns the identifier.
     * 
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(OrderInterface $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order;
    }
}
