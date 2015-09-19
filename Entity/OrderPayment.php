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
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var string
     */
    protected $notes;


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

    /**
     * Returns the notes.
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Sets the notes.
     *
     * @param string $notes
     * @return OrderPayment
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }
}
