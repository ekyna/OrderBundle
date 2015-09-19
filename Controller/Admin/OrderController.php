<?php

namespace Ekyna\Bundle\OrderBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvents;
use Ekyna\Bundle\PaymentBundle\Model\PaymentTransitionTrait;
use Ekyna\Component\Sale\Order\OrderPaymentInterface;
use Ekyna\Component\Sale\Payment\PaymentStates;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints;

/**
 * Class OrderController
 * @package Ekyna\Bundle\OrderBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderController extends ResourceController
{
    use PaymentTransitionTrait;

    /**
     * New payment action.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newPaymentAction(Request $request)
    {
        $context = $this->loadContext($request);
        /** @var \Ekyna\Component\Sale\Order\OrderInterface $order */
        $order = $context->getResource();

        $this->isGranted('EDIT', $order);

        $redirectPath = $this->generateResourcePath($order);

        $amount = $this->get('ekyna_order.order.calculator')->calculateOrderRemainingTotal($order);
        if ($amount <= 0) {
            $this->addFlash('ekyna_order.order.message.already_paid', 'info');
            return $this->redirect($redirectPath);
        }

        $paymentClass = $this->container->getParameter('ekyna_order.order_payment.class');
        /** @var \Ekyna\Component\Sale\Order\OrderPaymentInterface $payment */
        $payment = new $paymentClass;
        $payment
            ->setOrder($order)
            ->setAmount($amount)
        ;

        $action = $this->generateUrl(
            'ekyna_order_order_admin_payment_new',
            array('orderId' => $payment->getOrder()->getId())
        );
        $form = $this->createPaymentForm($payment, $action, $redirectPath);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $order->addPayment($payment);

            $event = new OrderEvent($order);
            $this->get('event_dispatcher')->dispatch(OrderEvents::CONTENT_CHANGE, $event);
            if (!$event->isPropagationStopped()) {
                return $this->redirect($redirectPath);
            }
        }

        $this->appendBreadcrumb(
            'order-payment-new',
            'ekyna_order.payment.button.new'
        );

        return $this->render('EkynaOrderBundle:Admin/Order/Payment:new.html.twig', array(
            'form' => $form->createView(),
            'form_template' => 'EkynaOrderBundle:Admin/Order/Payment:_form.html.twig',
            'order' => $order,
        ));
    }

    /**
     * Edit payment action.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editPaymentAction(Request $request)
    {
        $context = $this->loadContext($request);
        /** @var \Ekyna\Component\Sale\Order\OrderInterface $order */
        $order = $context->getResource();

        $this->isGranted('EDIT', $order);

        if (null === $payment = $order->findPaymentById($request->attributes->get('paymentId'))) {
            throw new NotFoundHttpException('Payment not found.');
        }

        $redirectPath = $this->generateResourcePath($order);
        $action = $this->generateUrl('ekyna_order_order_admin_payment_edit', array(
            'orderId' => $order->getId(),
            'paymentId' => $payment->getId(),
        ));
        $form = $this->createPaymentForm($payment, $action, $redirectPath);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $event = new OrderEvent($order);
            $this->get('event_dispatcher')->dispatch(OrderEvents::CONTENT_CHANGE, $event);
            if (!$event->isPropagationStopped()) {
                return $this->redirect($redirectPath);
            }
        }

        $this->appendBreadcrumb(
            'order-payment-edit',
            'ekyna_order.payment.button.edit'
        );

        return $this->render('EkynaOrderBundle:Admin/Order/Payment:edit.html.twig', array(
            'form' => $form->createView(),
            'form_template' => 'EkynaOrderBundle:Admin/Order/Payment:_form.html.twig',
            'order' => $order,
        ));
    }

    /**
     * Creates the payment form.
     *
     * @param OrderPaymentInterface $payment
     * @param string $action
     * @param string $cancelPath
     * @return \Symfony\Component\Form\Form
     */
    private function createPaymentForm(OrderPaymentInterface $payment, $action, $cancelPath)
    {
        $form = $this->createForm('ekyna_order_order_payment', $payment, array(
            'action' => $action,
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal form-with-tabs',
            ),
            'admin_mode' => true,
        ));
        $form->add('actions', 'form_actions', [
            'buttons' => [
                'save' => [
                    'type' => 'submit', 'options' => [
                        'button_class' => 'primary',
                        'label' => 'ekyna_core.button.save',
                        'attr' => [
                            'icon' => 'ok',
                        ],
                    ],
                ],
                'cancel' => [
                    'type' => 'button', 'options' => [
                        'label' => 'ekyna_core.button.cancel',
                        'button_class' => 'default',
                        'as_link' => true,
                        'attr' => [
                            'class' => 'form-cancel-btn',
                            'icon' => 'remove',
                            'href' => $cancelPath,
                        ],
                    ],
                ],
            ],
        ]);

        return $form;
    }

    /**
     * Remove payment action.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removePaymentAction(Request $request)
    {
        $context = $this->loadContext($request);
        /** @var \Ekyna\Component\Sale\Order\OrderInterface $order */
        $order = $context->getResource();

        $this->isGranted('EDIT', $order);

        if (null === $payment = $order->findPaymentById($request->attributes->get('paymentId'))) {
            throw new NotFoundHttpException('Payment not found.');
        }
        if ($payment->getState() !== PaymentStates::STATE_NEW) {
            throw new NotFoundHttpException('Payment is not new.');
        }

        $redirectPath = $this->generateResourcePath($order);
        $action = $this->generateUrl('ekyna_order_order_admin_payment_remove', array(
            'orderId' => $order->getId(),
            'paymentId' => $payment->getId(),
        ));
        $form = $this->createRemovePaymentForm($action, $redirectPath);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $order->removePayment($payment);

            $event = new OrderEvent($order);
            $this->get('event_dispatcher')->dispatch(OrderEvents::CONTENT_CHANGE, $event);
            if (!$event->isPropagationStopped()) {
                return $this->redirect($redirectPath);
            }
        }

        $this->appendBreadcrumb(
            'order-payment-remove',
            'ekyna_order.payment.button.remove'
        );

        return $this->render('EkynaOrderBundle:Admin/Order/Payment:remove.html.twig', array(
            'form' => $form->createView(),
            'order' => $order,
        ));
    }

    /**
     * Creates the remove payment form.
     *
     * @param string $action
     * @param string $cancelPath
     * @return \Symfony\Component\Form\Form
     */
    private function createRemovePaymentForm($action, $cancelPath)
    {
        return $this
            ->createFormBuilder(null, array(
                'action' => $action,
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
                'method' => 'POST',
                'admin_mode' => true,
                '_redirect_enabled' => true,
            ))
            ->add('confirm', 'checkbox', array(
                'label' => 'ekyna_order.payment.message.remove_confirm',
                'attr' => array('align_with_widget' => true),
                'required' => true,
                'constraints' => array(
                    new Constraints\True(),
                )
            ))
            ->add('actions', 'form_actions', [
                'buttons' => [
                    'remove' => [
                        'type' => 'submit', 'options' => [
                            'button_class' => 'danger',
                            'label' => 'ekyna_core.button.remove',
                            'attr' => [
                                'icon' => 'trash',
                            ],
                        ],
                    ],
                    'cancel' => [
                        'type' => 'button', 'options' => [
                            'label' => 'ekyna_core.button.cancel',
                            'button_class' => 'default',
                            'as_link' => true,
                            'attr' => [
                                'class' => 'form-cancel-btn',
                                'icon' => 'remove',
                                'href' => $cancelPath,
                            ],
                        ],
                    ],
                ],
            ])
            ->getForm()
        ;
    }

    /**
     * Transition payment action.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function transitionPaymentAction(Request $request)
    {
        $context = $this->loadContext($request);
        $resourceName = $this->config->getResourceName();

        /** @var \Ekyna\Component\Sale\Order\OrderInterface $order */
        $order = $context->getResource($resourceName);

        $this->isGranted('EDIT', $order);

        $payment = null;
        $paymentId = intval($request->attributes->get('paymentId'));
        foreach ($order->getPayments() as $p) {
            if ($p->getId() === $paymentId) {
                $payment = $p;
                break;
            }
        }
        if (null === $payment) {
            throw new NotFoundHttpException('Payment not found');
        }

        $this->applyPaymentTransition($payment, $request->attributes->get('transition'));

        return $this->redirect($this->generateUrl(
            $this->config->getRoute('show'),
            $context->getIdentifiers(true)
        ));
    }
}
