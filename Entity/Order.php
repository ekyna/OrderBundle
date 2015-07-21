<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\UserBundle\Model\AddressInterface;
use Ekyna\Bundle\UserBundle\Model\IdentityInterface;
use Ekyna\Bundle\UserBundle\Model\UserInterface;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;
use Ekyna\Component\Sale\Order\OrderPaymentInterface;
use Ekyna\Component\Sale\Order\OrderStates;
use Ekyna\Component\Sale\Order\OrderShipmentInterface;
use Ekyna\Component\Sale\Order\OrderTypes;
use Ekyna\Component\Sale\Payment\PaymentStates;
use Ekyna\Component\Sale\Shipment\ShipmentStates;

/**
 * Class Order
 * @package Ekyna\Bundle\OrderBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Order implements OrderInterface, IdentityInterface
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
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $gender;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var integer
     */
    protected $itemsCount;

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
     * @var integer
     */
    protected $totalWeight;

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
     * @var ArrayCollection|OrderItemInterface[]
     */
    protected $items;

    /**
     * @var ArrayCollection|OrderPaymentInterface[]
     */
    protected $payments;

    /**
     * @var ArrayCollection|OrderShipmentInterface[]
     */
    protected $shipments;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var AddressInterface
     */
    protected $invoiceAddress;

    /**
     * @var AddressInterface
     */
    protected $deliveryAddress;

    /**
     * @var boolean
     */
    protected $sameAddress;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->type          = OrderTypes::TYPE_ORDER;
        $this->locked        = false;
        $this->sameAddress   = true;

        $this->itemsCount    = 0;
        $this->currency      = 'EUR';
        $this->netTotal      = 0;
        $this->atiTotal      = 0;
        $this->totalWeight   = 0;

        $this->state         = OrderStates::STATE_NEW;
        $this->paymentState  = PaymentStates::STATE_PENDING;
        $this->shipmentState = ShipmentStates::STATE_PENDING;

        $this->items         = new ArrayCollection();
        $this->payments      = new ArrayCollection();
        $this->shipments     = new ArrayCollection();
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
     * {@inheritDoc}
     */
    public function setUpdated()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * {@inheritDoc}
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritDoc}
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritDoc}
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getNetShippingCost()
    {
        return 0; /* TODO */
    }

    /**
     * {@inheritDoc}
     */
    public function getAtiShippingCost()
    {
        return 0; /* TODO */
    }

    /**
     * {@inheritDoc}
     */
    public function getShippingTaxAmount()
    {
        return 0; /* TODO */
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * Sets the items.
     *
     * @param ArrayCollection|OrderItemInterface[] $items
     * @return Order
     */
    public function setItems($items)
    {
        foreach ($this->items as $item) {
            $item->setOrder(null);
        }
        foreach ($items as $item) {
            $item->setOrder($this);
        }
        $this->items = $items;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasItem(OrderItemInterface $item)
    {
        return $this->items->contains($item);
    }

    /**
     * {@inheritDoc}
     */
    public function addItem(OrderItemInterface $item)
    {
        foreach ($this->items as $i) {
            if ($i->equals($item)) {
                $i->merge($item);
                return $this;
            }
        }

        if (!$this->hasItem($item)) {
            $item->setOrder($this);
            $this->items->add($item);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeItem(OrderItemInterface $item)
    {
        foreach ($this->items as $i) {
            if ($i->equals($item)) {
                $i->setOrder(null);
                $this->items->removeElement($i);
                return $this;
            }
        }

        if ($this->hasItem($item)) {
            $item->setOrder(null);
            $this->items->removeElement($item);
        }

        return $this;
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
        return 0 < $this->getTotalWeight();
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return 0 === $this->items->count();
    }

    /**
     * Sets the payments.
     *
     * @param ArrayCollection|OrderPaymentInterface[] $payments
     * @return Order
     */
    public function setPayments($payments)
    {
        foreach($this->payments as $payment) {
            $payment->setOrder(null);
        }
        foreach($payments as $payment) {
            $payment->setOrder($this);
        }
        $this->payments = $payments;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasPayment(OrderPaymentInterface $payment)
    {
        return $this->payments->contains($payment);
    }

    /**
     * {@inheritDoc}
     */
    public function addPayment(OrderPaymentInterface $payment)
    {
        if (!$this->hasPayment($payment)) {
            $payment->setOrder($this);
            $this->payments->add($payment);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removePayment(OrderPaymentInterface $payment)
    {
        if ($this->hasPayment($payment)) {
            $payment->setOrder(null);
            $this->payments->removeElement($payment);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Sets the shipments.
     *
     * @param ArrayCollection|OrderShipmentInterface[] $shipments
     * @return Order
     */
    public function setShipments($shipments)
    {
        foreach ($this->shipments as $shipment) {
            $shipment->setOrder(null);
        }
        foreach ($shipments as $shipment) {
            $shipment->setOrder($this);
        }
        $this->shipments = $shipments;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasShipment(OrderShipmentInterface $shipment)
    {
        return $this->shipments->contains($shipment);
    }

    /**
     * {@inheritDoc}
     */
    public function addShipment(OrderShipmentInterface $shipment)
    {
        if (!$this->hasShipment($shipment)) {
            $shipment->setOrder($this);
            $this->shipments->add($shipment);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeShipment(OrderShipmentInterface $shipment)
    {
        if ($this->hasShipment($shipment)) {
            $shipment->setOrder(null);
            $this->shipments->removeElement($shipment);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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

    /**
     * {@inheritDoc}
     */
    public function setSameAddress($sameAddress)
    {
        $this->sameAddress = $sameAddress;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSameAddress()
    {
        return $this->sameAddress;
    }
}
