<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;
use Ekyna\Component\Sale\Tax\TaxInterface;

/**
 * Class OrderItem
 * @package Ekyna\Bundle\OrderBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItem implements OrderItemInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $designation;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var TaxInterface
     */
    protected $tax;

    /**
     * @var float
     */
    protected $weight;

    /**
     * @var integer
     */
    protected $quantity;

    /**
     * @var integer
     */
    protected $position;

    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var string
     */
    protected $subjectType;

    /**
     * @var array
     */
    protected $subjectData;

    /**
     * @var object
     */
    protected $subject;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->quantity = 1;
        $this->position = 0;

        $this->subjectData = [];
    }

    /**
     * Returns the identifier.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * {@inheritdoc}
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->price = floatval($price);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setTax(TaxInterface $tax = null)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
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

    /**
     * {@inheritdoc}
     */
    public function equals(OrderItemInterface $orderItem)
    {
        // By subject type and data
        if (0 < strlen($this->subjectType) && !empty($this->subjectData)) {
            return $orderItem->getSubjectType() === $this->subjectType
            && $orderItem->getSubjectData() === $this->subjectData;
        }

        // By reference
        return $orderItem->getReference() === $this->reference;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(OrderItemInterface $orderItem)
    {
        if ($this->equals($orderItem)) {
            $this->setQuantity($this->getQuantity() + $orderItem->getQuantity());
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubjectType($subjectType)
    {
        $this->subjectType = $subjectType;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectType()
    {
        return $this->subjectType;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubjectData(array $subjectData = [])
    {
        $this->subjectData = $subjectData;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectData()
    {
        return $this->subjectData;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
