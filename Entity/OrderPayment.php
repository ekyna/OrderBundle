<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Ekyna\Bundle\PaymentBundle\Entity\Payment;
use Ekyna\Component\Sale\Order\OrderPaymentInterface;

/**
 * OrderPayment.
 *
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
     * Sets the order.
     * 
     * @param Order $order
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\OrderPayment
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Returns the order.
     * 
     * @return \Ekyna\Bundle\orderBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
}
