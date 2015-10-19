<?php

namespace Ekyna\Bundle\OrderBundle\EventListener;

use Ekyna\Bundle\AdminBundle\Event\ResourceMessage;
use Ekyna\Bundle\AdminBundle\Operator\ResourceOperatorInterface;
use Ekyna\Bundle\OrderBundle\Entity\OrderPayment;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Ekyna\Bundle\OrderBundle\Exception\LogicException;
use Ekyna\Bundle\OrderBundle\Service\CalculatorInterface;
use Ekyna\Bundle\OrderBundle\Service\StateResolverInterface;
use Ekyna\Bundle\PaymentBundle\Event\PaymentEvent;
use Ekyna\Bundle\PaymentBundle\Event\PaymentEvents;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderTypes;
use SM\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @var CalculatorInterface
     */
    private $calculator;

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
     * @var AuthorizationCheckerInterface
     */
    private $authorization;


    /**
     * Constructor.
     *
     * @param ResourceOperatorInterface      $operator
     * @param CalculatorInterface            $calculator
     * @param StateResolverInterface         $stateResolver
     * @param FactoryInterface               $factory
     * @param UrlGeneratorInterface          $urlGenerator
     * @param AuthorizationCheckerInterface  $authorization
     */
    public function __construct(
        ResourceOperatorInterface      $operator,
        CalculatorInterface            $calculator,
        StateResolverInterface         $stateResolver,
        FactoryInterface               $factory,
        UrlGeneratorInterface          $urlGenerator,
        AuthorizationCheckerInterface  $authorization
    ) {
        $this->operator      = $operator;
        $this->calculator    = $calculator;
        $this->stateResolver = $stateResolver;
        $this->factory       = $factory;
        $this->urlGenerator  = $urlGenerator;
        $this->authorization = $authorization;
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
            throw new LogicException('Empty order.');
        }

        if ($order->getLocked()) {
            $event->addMessage(new ResourceMessage('ekyna_order.event.locked', ResourceMessage::TYPE_ERROR));
            return;
        }
        $order->setLocked(true);

        if (0 >= $payment->getAmount()) {
            $payment->setAmount(
                $this->calculator->calculateOrderRemainingTotal($order)
            );
        }

        $this->updateOrder($order, $event, true);
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

        $this->updateOrder($order, $event, true);
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

        $order = $payment->getOrder();
        if ($order->getLocked()) {
            $order->setLocked(false);
            $this->updateOrder($order, $event);
        }

        $returnPath = null;
        $details = $payment->getDetails();
        if (array_key_exists('done_redirect_path', $details)) {
            $returnPath = $details['done_redirect_path'];
        } else {
            $type = $order->getType();
            if ($type === OrderTypes::TYPE_CART) {
                $returnPath = $this->urlGenerator->generate('ekyna_cart_payment');
            } elseif ($type === OrderTypes::TYPE_QUOTE) {
                throw new \BadMethodCallException('Not yet implemented');
            } elseif ($type === OrderTypes::TYPE_ORDER) {
                if ($this->authorization->isGranted('ROLE_ADMIN')) {
                    $returnPath = $this->urlGenerator->generate(
                        'ekyna_order_order_admin_show',
                        array('orderId' => $order->getId())
                    );
                } else {
                    $returnPath = $this->urlGenerator->generate(
                        'ekyna_cart_confirmation',
                        array(
                            'orderKey' => $order->getKey(),
                            'paymentId' => $payment->getId(),
                        )
                    );
                }
            }
        }

        if (null === $returnPath) {
            throw new LogicException('Unable to determine return path.');
        }

        $event->setResponse(new RedirectResponse($returnPath));
    }

    /**
     * Updates the order.
     *
     * @param OrderInterface $order
     * @param PaymentEvent   $event
     * @param bool           $force
     */
    private function updateOrder(OrderInterface $order, PaymentEvent $event, $force = false)
    {
        $orderEvent = new OrderEvent($order);
        $orderEvent->setForce($force);

        $this->operator->update($orderEvent);

        if ($orderEvent->hasMessages()) {
            $event->addMessages($orderEvent->getMessages());
        }
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return [
            PaymentEvents::PREPARE      => ['onPaymentPrepare',     0],
            PaymentEvents::STATE_CHANGE => ['onPaymentStateChange', 0],
            PaymentEvents::NOTIFY       => ['onPaymentNotify',      0],
            PaymentEvents::DONE         => ['onPaymentDone',        0],
        ];
    }
}
