<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\UserBundle\Model\UserInterface;
use Ekyna\Bundle\UserBundle\Model\AddressInterface;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;
use Ekyna\Component\Sale\Order\OrderStatuses;
use Ekyna\Component\Sale\TaxesAmounts;
use Ekyna\Component\Sale\Product\ProductTypes;

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
    protected $status;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

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
        $this->items = new ArrayCollection();
        $this->status = OrderStatuses::CART;
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * Sets the "all taxes excluded" total.
     *
     * @param float $netTotal
     * 
     * @return Order
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
     * @return Order
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

    /**
     * Sets the status.
     *
     * @param integer $status
     * 
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the "expires at" datetime.
     *
     * @param \DateTime $expiresAt
     * 
     * @return Order
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Sets the "completed at" datetime.
     *
     * @param \DateTime $completedAt
     * 
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * @return Order
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
     * Returns wether the order has the given item or not.
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
     * @return Order
     */
    public function addItem(OrderItemInterface $orderItem)
    {
        if($this->hasItem($orderItem)) {
            return $this;
        }

        $this->setUpdatedAt(new \DateTime());

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
        $this->setUpdatedAt(new \DateTime());
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
     * Sets the user.
     *
     * @param \Ekyna\Bundle\UserBundle\Model\UserInterface $user
     * 
     * @return Order
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
     * @return Order
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
     * @return Order
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
