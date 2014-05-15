<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\UserBundle\Model\AddressInterface;
use Ekyna\Bundle\UserBundle\Model\UserInterface;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;
use Ekyna\Component\Sale\Order\OrderPaymentInterface;
use Ekyna\Component\Sale\Order\OrderStates;
use Ekyna\Component\Sale\Order\OrderShipmentInterface;
use Ekyna\Component\Sale\Payment\PaymentStates;
use Ekyna\Component\Sale\Product\ProductTypes;
use Ekyna\Component\Sale\TaxesAmounts;
use Ekyna\Component\Sale\Shipment\ShipmentStates;

/**
 * Order
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Order implements OrderInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var integer
     */
    protected $itemsCount;

    /**
     * @var integer
     */
    protected $totalWeight;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var float
     */
    protected $netTotal;

    /**
     * @var float
     */
    protected $atiTotal;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $paymentState;

    /**
     * @var string
     */
    protected $shipmentState;

    /**
     * @var \DateTime
     */
    protected $completedAt;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $deletedAt;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $items;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $payments;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $shipments;

    /**
     * @var \Ekyna\Bundle\UserBundle\Model\UserInterface
     */
    protected $user;

    /**
     * @var \Ekyna\Bundle\UserBundle\Model\AddressInterface
     */
    protected $invoiceAddress;

    /**
     * @var \Ekyna\Bundle\UserBundle\Model\AddressInterface
     */
    protected $deliveryAddress;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->type   = OrderInterface::TYPE_ORDER;
        $this->locked = false;

        $this->currency = 'EUR';

        $this->items     = new ArrayCollection();
        $this->payments  = new ArrayCollection();
        $this->shipments = new ArrayCollection();

        $this->state         = OrderStates::STATE_NEW;
        $this->paymentState  = PaymentStates::STATE_PENDING;
        $this->shipmentState = ShipmentStates::STATE_PENDING;
    }

    /**
     * Returns the string representation.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getNumber();
    }

    /**
     * Update flag to trigger doctrine update vent listener
     */
    public function setUpdated()
    {
        $this->updatedAt = new \DateTime();
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
     * Sets the number.
     *
     * @param string $number
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Sets the items count.
     *
     * @param integer $itemCount
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setItemsCount($count)
    {
        $this->itemsCount = $count;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsCount()
    {
        return $this->itemsCount;
    }

    /**
     * Sets the total weight.
     *
     * @param integer $weight
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setTotalWeight($weight)
    {
        $this->totalWeight = $weight;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalWeight()
    {
        return $this->totalWeight;
    }

    /**
     * Sets the currency.
     *
     * @param string $currency
     *
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets the "all taxes excluded" total.
     *
     * @param float $netTotal
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setNetTotal($netTotal)
    {
        $this->netTotal = $netTotal;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNetTotal()
    {
        return $this->netTotal;
    }

    /**
     * Sets the "all taxes included" total.
     *
     * @param float $atiTotal
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setAtiTotal($atiTotal)
    {
        $this->atiTotal = $atiTotal;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAtiTotal()
    {
        return $this->atiTotal;
    }

    /**
     * Returns the taxes amounts.
     *
     * @return \Ekyna\Component\Sale\TaxesAmounts
     */
    public function getTaxesAmounts()
    {
        $taxesAmounts = new TaxesAmounts();
        foreach($this->items as $item) {
            $taxesAmounts->merge($item->getTotalTaxesAmounts());
        }

        return $taxesAmounts;
    }

    public function getNetShippingCost()
    {
        return 0;
    }

    public function getAtiShippingCost()
    {
        return 0;
    }

    public function getShippingTaxAmount()
    {
        return 0;
    }
    
	/**
	 * Sets the type.
	 * 
	 * @param string $type
	 * 
	 * @return \Ekyna\Bundle\OrderBundle\Entity\Order
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Sets whether the order is locked.
	 * 
	 * @param boolean $locked
	 * 
	 * @return \Ekyna\Bundle\OrderBundle\Entity\Order
	 */
	public function setLocked($locked)
	{
		$this->locked = (bool) $locked;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLocked()
	{
		return $this->locked;
	}

    /**
     * Sets the state.
     *
     * @param string $state
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the payment state.
     *
     * @param string $paymentState
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setPaymentState($paymentState)
    {
        $this->paymentState = $paymentState;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentState()
    {
        return $this->paymentState;
    }

    /**
     * Sets the shipment state.
     *
     * @param string $shipmentState
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setShipmentState($shipmentState)
    {
        $this->shipmentState = $shipmentState;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShipmentState()
    {
        return $this->shipmentState;
    }

    /**
     * Sets the "completed at" datetime.
     *
     * @param \DateTime $completedAt
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setCompletedAt(\DateTime $completedAt = null)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Sets the "created at" datetime.
     *
     * @param \DateTime $createdAt
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the "updated at" datetime.
     *
     * @param \DateTime $updatedAt
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the "deleted at" datetime.
     *
     * @param \DateTime $deletedAt
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setDeletedAt(\DateTime $deletedAt = null)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Returns whether the order has the given item or not.
     *
     * @param \Ekyna\Component\Sale\Order\OrderItemInterface $orderItem
     *
     * @return boolean
     */
    public function hasItem(OrderItemInterface $orderItem)
    {
        return $this->items->contains($orderItem);
    }

    /**
     * Adds an item.
     *
     * @param \Ekyna\Component\Sale\Order\OrderItemInterface $orderItem
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function addItem(OrderItemInterface $orderItem)
    {
        if($this->hasItem($orderItem)) {
            return $this;
        }

        foreach($this->items as $item) {
            if($item->equals($orderItem)) {
                $item->merge($orderItem);
                return $this;
            }
        }

        $orderItem->setOrder($this);
        $this->items->add($orderItem);

        return $this;
    }

    /**
     * Removes the item.
     *
     * @param \Ekyna\Component\Sale\Order\OrderItemInterface $orderItem
     */
    public function removeItem(OrderItemInterface $orderItem)
    {
        $this->items->removeElement($orderItem);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresShipment()
    {
        foreach ($this->items as $item) {
            if (null !== $product = $item->getProduct()) {
               if ($product->getType() === ProductTypes::PHYSICAL) {
                   return true;
               } 
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return 0 === $this->items->count();
    }

    /**
     * Returns whether the order has the given payment or not.
     *
     * @param \Ekyna\Component\Sale\Order\OrderPaymentInterface $orderPayment
     *
     * @return boolean
     */
    public function hasPayment(OrderPaymentInterface $orderPayment)
    {
        return $this->payments->contains($orderPayment);
    }

    /**
     * Adds a payment.
     *
     * @param \Ekyna\Component\Sale\Order\OrderPaymentInterface $payment
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function addPayment(OrderPaymentInterface $orderPayment)
    {
        if($this->hasPayment($orderPayment)) {
            return $this;
        }

        $orderPayment->setOrder($this);
        $this->payments->add($orderPayment);

        return $this;
    }

    /**
     * Removes the payment.
     *
     * @param \Ekyna\Component\Sale\Order\OrderPaymentInterface $orderPayment
     */
    public function removePayment(OrderPaymentInterface $orderPayment)
    {
        $this->payments->removeElement($orderPayment);
    }

    /**
     * {@inheritdoc}
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Returns whether the order has the given shipment or not.
     *
     * @param \Ekyna\Component\Sale\Order\OrderShipmentInterface $orderShipment
     *
     * @return boolean
     */
    public function hasShipment(OrderShipmentInterface $orderShipment)
    {
        return $this->shipments->contains($orderShipment);
    }

    /**
     * Adds a shipment.
     *
     * @param \Ekyna\Component\Sale\Order\OrderShipmentInterface $shipment
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function addShipment(OrderShipmentInterface $orderShipment)
    {
        if($this->hasShipment($orderShipment)) {
            return $this;
        }

        $orderShipment->setOrder($this);
        $this->shipments->add($orderShipment);

        return $this;
    }

    /**
     * Removes the shipment.
     *
     * @param \Ekyna\Component\Sale\Order\OrderShipmentInterface $orderShipment
     */
    public function removeShipment(OrderShipmentInterface $orderShipment)
    {
        $this->shipments->removeElement($orderShipment);
    }

    /**
     * {@inheritdoc}
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * Sets the user.
     *
     * @param \Ekyna\Bundle\UserBundle\Model\UserInterface $user
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the invoice address.
     *
     * @param \Ekyna\Bundle\UserBundle\Model\AddressInterface $address
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setInvoiceAddress(AddressInterface $address = null)
    {
        $this->invoiceAddress = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceAddress()
    {
        return $this->invoiceAddress;
    }

    /**
     * Sets the delivery address.
     *
     * @param \Ekyna\Bundle\UserBundle\Model\AddressInterface $address
     * 
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setDeliveryAddress(AddressInterface $address = null)
    {
        $this->deliveryAddress = $address;

        return $this;
    }

    /**
     * {@inheritdoc} 
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }
}
