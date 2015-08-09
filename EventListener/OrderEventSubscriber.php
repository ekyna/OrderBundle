<?php

namespace Ekyna\Bundle\OrderBundle\EventListener;

use Ekyna\Bundle\AdminBundle\Event\ResourceMessage;
use Ekyna\Bundle\AdminBundle\Operator\ResourceOperatorInterface;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvents;
use Ekyna\Bundle\OrderBundle\Exception\OrderException;
use Ekyna\Bundle\OrderBundle\Service\CalculatorInterface;
use Ekyna\Bundle\OrderBundle\Service\GeneratorInterface;
use Ekyna\Bundle\OrderBundle\Service\StateResolverInterface;
use Ekyna\Bundle\UserBundle\Model\AddressInterface;
use Ekyna\Bundle\UserBundle\Model\UserInterface;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderStates;
use Ekyna\Component\Sale\Order\OrderTypes;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class OrderEventSubscriber
 * @package Ekyna\Bundle\OrderBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderEventSubscriber extends AbstractEventSubscriber
{
    /**
     * @var ResourceOperatorInterface
     */
    private $orderOperator;

    /**
     * @var ResourceOperatorInterface
     */
    private $addressOperator;

    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var StateResolverInterface
     */
    private $stateResolver;

    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var bool
     */
    private $debug;


    /**
     * Constructor.
     *
     * @param ResourceOperatorInterface $orderOperator
     * @param ResourceOperatorInterface $addressOperator
     * @param CalculatorInterface       $calculator
     * @param StateResolverInterface    $stateResolver
     * @param GeneratorInterface        $generator
     * @param ValidatorInterface        $validator
     * @param bool                      $debug
     */
    public function __construct(
        ResourceOperatorInterface $orderOperator,
        ResourceOperatorInterface $addressOperator,
        CalculatorInterface       $calculator,
        StateResolverInterface    $stateResolver,
        GeneratorInterface        $generator,
        ValidatorInterface        $validator,
        $debug = false
    ) {
        $this->orderOperator   = $orderOperator;
        $this->addressOperator = $addressOperator;
        $this->calculator      = $calculator;
        $this->stateResolver   = $stateResolver;
        $this->generator       = $generator;
        $this->validator       = $validator;
        $this->debug           = $debug;
    }

    /**
     * Pre content change event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreContentChange(OrderEvent $event)
    {
        $this->isOrderLocked($event);
    }

    /**
     * Content change event handler.
     *
     * @param OrderEvent $event
     */
    public function onContentChange(OrderEvent $event)
    {
        $order = $event->getOrder();

        $this->calculator->updateTotals($order);
        $this->stateResolver->resolve($order, $event);
    }

    /**
     * Post content change event handler.
     *
     * @param OrderEvent $event
     */
    public function onPostContentChange(OrderEvent $event)
    {
        $this->orderOperator->update($event);
    }

    /**
     * State change event handler.
     *
     * @param OrderEvent $event
     * @throws OrderException
     */
    public function onStateChange(OrderEvent $event)
    {
        $order = $event->getOrder();

        // If new state is Accepted or Completed and type is not order
        if (in_array($order->getState(), array(OrderStates::STATE_ACCEPTED, OrderStates::STATE_COMPLETED))
            && $order->getType() != OrderTypes::TYPE_ORDER
        ) {
            // Transform to an order
            $order
                ->setType(OrderTypes::TYPE_ORDER)
                ->setCreatedAt(new \DateTime())
                ->setNumber(null)
                ->setKey(null)
            ;

            $this->generateNumberAndKey($order);
            $this->handleAddresses($order);

            $errorList = $this->validator->validate($order, array('Default', 'Order'));
            if ($errorList->count() > 0) {
                $messages = [];
                foreach ($errorList as $error) {
                    $messages[] = $error->getMessage();
                }
                throw new OrderException('Invalid order : ' . implode('<br>', $messages));
            }
        }

        // Set completed at
        if ($order->getState() === OrderStates::STATE_COMPLETED && null === $order->getCompletedAt()) {
            $order->setCompletedAt(new \DateTime());
        }
    }

    /**
     * Post state change event handler.
     *
     * @param OrderEvent $event
     */
    public function onPostStateChange(OrderEvent $event)
    {
        $this->orderOperator->update($event);
    }

    /**
     * Pre create event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreCreate(OrderEvent $event)
    {
        if ($this->isOrderLocked($event)) {
            return;
        }

        $order = $event->getOrder();
        if ($order->getType() === OrderTypes::TYPE_CART) {
            return;
        }

        // Generate number and key
        $this->generateNumberAndKey($order);

        // Handle identity
        $this->handleIdentity($order);

        // Handle addresses
        $this->handleAddresses($order);
    }

    /**
     * Pre update event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreUpdate(OrderEvent $event)
    {
        if ($this->isOrderLocked($event)) {
            return;
        }

        $order = $event->getOrder();
        if ($order->getType() === OrderTypes::TYPE_CART) {
            return;
        }

        // Generate number and key
        $this->generateNumberAndKey($order);

        // Handle identity
        $this->handleIdentity($order);

        // Handle addresses
        $this->handleAddresses($order);
    }

    /**
     * Pre delete event handler.
     *
     * @param OrderEvent $event
     */
    public function onPreDelete(OrderEvent $event)
    {
        $this->isOrderLocked($event);

        $order = $event->getOrder();

        if (!$event->getHard()) {
            // Stop if order has payments
            if (0 < $order->getPayments()->count()) {
                $event->addMessage(new ResourceMessage(
                    'ekyna_order.order.message.has_payment_cant_deleted',
                    ResourceMessage::TYPE_ERROR
                ));
                return;
            }
            // Stop if order has shipments
            if (0 < $order->getShipments()->count()) {
                $event->addMessage(new ResourceMessage(
                    'ekyna_order.order.message.has_shipment_cant_deleted',
                    ResourceMessage::TYPE_ERROR
                ));
                return;
            }
        } else {
            // Delete unused cloned invoice address.
            if (null !== $invoiceAddress = $order->getInvoiceAddress()) {
                if (null === $invoiceAddress->getUser()) {
                    $this->addressOperator->delete($invoiceAddress);
                }
            }
            // Delete unused cloned delivery address.
            if (null !== $deliveryAddress = $order->getDeliveryAddress()) {
                if (null === $deliveryAddress->getUser()) {
                    $this->addressOperator->delete($deliveryAddress);
                }
            }
        }
    }

    /**
     * Handle the identity.
     *
     * @param OrderInterface $order
     * @throws OrderException
     */
    private function handleIdentity(OrderInterface $order)
    {
        if (null === $order->getEmail()
            || null === $order->getGender()
            || null === $order->getFirstName()
            || null === $order->getLastName()
        ) {
            if (null === $user = $order->getUser()) {
                throw new OrderException('User is not set.');
            }
            if (null === $order->getEmail()) {
                $order->setEmail($user->getEmail());
            }
            if (null === $order->getGender()) {
                $order->setGender($user->getGender());
            }
            if (null === $order->getFirstName()) {
                $order->setFirstName($user->getFirstName());
            }
            if (null === $order->getLastName()) {
                $order->setLastName($user->getLastName());
            }
        }
    }

    /**
     * handle the addresses.
     *
     * @param OrderInterface $order
     * @throws OrderException
     */
    private function handleAddresses(OrderInterface $order)
    {
        // If invoice address is no set : use the user's default one.
        if (null === $order->getInvoiceAddress()) {
            $order->setInvoiceAddress(
                $this->getOrderUserDefaultAddress($order)
            );
        }

        // If delivery address is no set and not "same delivery address" : use the user's default one.
        if ((null === $order->getDeliveryAddress()) && !$order->getSameAddress()) {
            $order->setDeliveryAddress(
                $this->getOrderUserDefaultAddress($order)
            );
        }
        // Else if delivery address is set and "same delivery address"
        elseif ((null !== $deliveryAddress = $order->getDeliveryAddress()) && $order->getSameAddress()) {
            // Unset delivery address
            $order->setDeliveryAddress(null);
            // If the address is a clone (do not belong to a user)
            if (null === $deliveryAddress->getUser()) {
                // Delete the address
                $this->addressOperator->delete($deliveryAddress);
            }
        }

        // If type is order or quote, clone the addresses.
        if (in_array($order->getType(), array(OrderTypes::TYPE_ORDER, OrderTypes::TYPE_QUOTE))) {
            // Clone invoice address if needed
            $invoiceAddress = $order->getInvoiceAddress();
            if (null !== $user = $invoiceAddress->getUser()) {
                $invoiceAddress = clone $invoiceAddress;
                $this->handleAddressIdentity($invoiceAddress, $user);
                $invoiceAddress->setUser(null);
                $order->setInvoiceAddress($invoiceAddress);
            }
            // Clone delivery address if needed
            if (!$order->getSameAddress()) {
                $deliveryAddress = $order->getDeliveryAddress();
                if (null !== $user = $deliveryAddress->getUser()) {
                    $deliveryAddress = clone $deliveryAddress;
                    $this->handleAddressIdentity($deliveryAddress, $user);
                    $deliveryAddress->setUser(null);
                    $order->setDeliveryAddress($deliveryAddress);
                }
            }
        }
    }

    /**
     * Fills the address identity fields if needed.
     *
     * @param AddressInterface $address
     * @param UserInterface $user
     */
    private function handleAddressIdentity(AddressInterface $address, UserInterface $user)
    {
        if (null === $address->getGender()) {
            $address->setGender($user->getGender());
        }
        if (null === $address->getFirstName()) {
            $address->setFirstName($user->getFirstName());
        }
        if (null === $address->getLastName()) {
            $address->setLastName($user->getLastName());
        }
    }

    /**
     * Returns the default address of the order's user.
     *
     * @param OrderInterface $order
     * @return mixed
     * @throws OrderException
     */
    private function getOrderUserDefaultAddress(OrderInterface $order)
    {
        if (null === $user = $order->getUser()) {
            throw new OrderException('User is not set.');
        }
        if (null === $address = $user->getAddresses()->first()) { // TODO Default address
            throw new OrderException('Unable to find the user default address.');
        }
        return $address;
    }

    /**
     * Generates the order number and key.
     *
     * @param OrderInterface $order
     */
    private function generateNumberAndKey(OrderInterface $order)
    {
        // Only for orders and quotes
        if (!in_array($order->getType(), array(OrderTypes::TYPE_ORDER, OrderTypes::TYPE_QUOTE))) {
            return;
        }

        $this->generator
            ->generateNumber($order)
            ->generateKey($order)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            OrderEvents::CONTENT_CHANGE => array(
                array('onPreContentChange',   512),
                array('onContentChange',      0),
                array('onPostContentChange', -512),
            ),
            OrderEvents::STATE_CHANGE   => array(
                array('onStateChange',      0),
                array('onPostStateChange', -512),
            ),
            OrderEvents::PRE_CREATE     => array('onPreCreate', 0),
            OrderEvents::PRE_UPDATE     => array('onPreUpdate', 0),
            OrderEvents::PRE_DELETE     => array('onPreDelete', 0),
        );
    }
}
