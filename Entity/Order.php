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
     * {@inheritDoc}
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Sets the items count.
     *
     * @param integer $count
     * @return \Ekyna\Bundle\OrderBundle\Entity\Order
     */
    public function setItemsCount($count)
    {
        $this->itemsCount = $count;

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType()
	{
		return $this->type;
	}

    /**
     * {@inheritDoc}
     */
	public function setLocked($locked)
	{
		$this->locked = (bool) $locked;

		return $this;
	}

	/**
	 * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function addItem(OrderItemInterface $orderItem)
    {
        if ($this->hasItem($orderItem)) {
            return $this;
        }

        /** @var OrderItemInterface $item */
        foreach ($this->items as $item) {
            if ($item->equals($orderItem)) {
                $item->merge($orderItem);
                return $this;
            }
        }

        $orderItem->setOrder($this);
        $this->items->add($orderItem);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeItem(OrderItemInterface $orderItem)
    {
        $orderItem->setOrder(null);
        $this->items->removeElement($orderItem);
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritDoc}
     */
    public function requiresShipment()
    {
        /** @var OrderItemInterface $item */
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
     * {@inheritDoc}
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
     * @param \Ekyna\Component\Sale\Order\OrderPaymentInterface $orderPayment
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
        $orderPayment->setOrder(null);
        $this->payments->removeElement($orderPayment);
    }

    /**
     * {@inheritDoc}
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
     * @param \Ekyna\Component\Sale\Order\OrderShipmentInterface $orderShipment
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc} 
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }
}
