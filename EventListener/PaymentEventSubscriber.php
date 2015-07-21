<?php

namespace Ekyna\Bundle\OrderBundle\EventListener;

use Ekyna\Bundle\AdminBundle\Event\ResourceMessage;
use Ekyna\Bundle\AdminBundle\Operator\ResourceOperatorInterface;
use Ekyna\Bundle\OrderBundle\Entity\OrderPayment;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Ekyna\Bundle\OrderBundle\Exception\LogicException;
use Ekyna\Bundle\OrderBundle\Service\StateResolverInterface;
use Ekyna\Bundle\PaymentBundle\Event\PaymentEvent;
use Ekyna\Bundle\PaymentBundle\Event\PaymentEvents;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderTypes;
use SM\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class PaymentEventSubscriber
 * @package Ekyna\Bundle\OrderBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PaymentEventSubscriber extends AbstractEventSubscriber
{
    /**
     * @var ResourceOperatorInterface
     */
    private $operator;

    /**
     * @var StateResolverInterface
     */
    private $stateResolver;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;


    /**
     * Constructor.
     *
     * @param ResourceOperatorInterface $operator
     * @param StateResolverInterface    $stateResolver
     * @param FactoryInterface          $factory
     * @param UrlGeneratorInterface     $urlGenerator
     * @param SecurityContextInterface  $securityContext
     */
    public function __construct(
        ResourceOperatorInterface $operator,
        StateResolverInterface    $stateResolver,
        FactoryInterface          $factory,
        UrlGeneratorInterface     $urlGenerator,
        SecurityContextInterface  $securityContext
    ) {
        $this->operator        = $operator;
        $this->stateResolver   = $stateResolver;
        $this->factory         = $factory;
        $this->urlGenerator    = $urlGenerator;
        $this->securityContext = $securityContext;
    }

    /**
     * Payment prepare event handler.
     *
     * @param PaymentEvent $event
     * @throws LogicException
     */
    public function onPaymentPrepare(PaymentEvent $event)
    {
        $payment = $event->getPayment();
        if (!$payment instanceof OrderPayment) {
            return;
        }

        if (null === $order = $payment->getOrder()) {
            throw new LogicException('You must assign the payment to the order first.');
        }

        if ($order->isEmpty()) {
            throw new LogicException('Empty order related payment.');
        }

        if ($order->getLocked()) {
            $event->addMessage(new ResourceMessage('ekyna_order.event.locked', ResourceMessage::TYPE_ERROR));
            return;
        }
        $order->setLocked(true);

        $payment->setAmount($order->getAtiTotal()); // TODO remaining amount (minus paid amount)

        $this->updateOrder($order, $event);
        // TODO propagation stopped / errors ?
    }

    /**
     * Payment state change event handler.
     *
     * @param PaymentEvent $event
     */
    public function onPaymentStateChange(PaymentEvent $event)
    {
        $payment = $event->getPayment();
        if (!$payment instanceof OrderPayment) {
            return;
        }

        $order = $payment->getOrder();

        $this->stateResolver->resolve($order, $event);

        $this->updateOrder($order, $event);
        // TODO propagation stopped / errors ?
    }

    /**
     * Payment notify event handler.
     *
     * @param PaymentEvent $event
     */
    public function onPaymentNotify(PaymentEvent $event)
    {
        $payment = $event->getPayment();
        if (!$payment instanceof OrderPayment) {
            return;
        }

        $order = $payment->getOrder();

        if ($order->getLocked()) {
            $order->setLocked(false);
            $this->updateOrder($order, $event);
            // TODO propagation stopped / errors ?
        }
    }

    /**
     * Payment done event handler.
     *
     * @param PaymentEvent $event
     * @throws LogicException
     */
    public function onPaymentDone(PaymentEvent $event)
    {
        $payment = $event->getPayment();
        if (!$payment instanceof OrderPayment) {
            return;
        }

        $details = $payment->getDetails();
        if (array_key_exists('done_redirect_path', $details)) {
            $event->setResponse(new RedirectResponse($details['done_redirect_path']));
            return;
        }

        $order = $payment->getOrder();
        $type = $order->getType();

        if ($type === OrderTypes::TYPE_CART) {
            $returnPath = $this->urlGenerator->generate('ekyna_cart_payment');
        } elseif ($type === OrderTypes::TYPE_QUOTE) {
            throw new \BadMethodCallException('Not yet implemented');
        } elseif ($type === OrderTypes::TYPE_ORDER) {
            if ($this->securityContext->isGranted('ROLE_ADMIN')) {
                $returnPath = $this->urlGenerator->generate('ekyna_order_order_admin_show', array('orderId' => $order->getId()));
            } else {
                $returnPath = $this->urlGenerator->generate('ekyna_cart_confirmation', array('key' => $order->getKey()));
            }
        } else {
            throw new LogicException(sprintf('Invalid order type "%s".', $type));
        }

        if ($order->getLocked()) {
            $order->setLocked(false);
            $this->updateOrder($order, $event);
        }

        $event->setResponse(new RedirectResponse($returnPath));
    }

    /**
     * Updates the order.
     *
     * @param OrderInterface $order
     * @param PaymentEvent $event
     */
    private function updateOrder(OrderInterface $order, PaymentEvent $event)
    {
        $orderEvent = new OrderEvent($order);
        $this->operator->update($orderEvent);
        $event->addMessages($orderEvent->getMessages());
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            PaymentEvents::PREPARE      => array('onPaymentPrepare', 0),
            PaymentEvents::STATE_CHANGE => array('onPaymentStateChange', 0),
            PaymentEvents::NOTIFY       => array('onPaymentNotify', 0),
            PaymentEvents::DONE         => array('onPaymentDone', 0),
        );
    }
}
