<?php

namespace Ekyna\Bundle\OrderBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Ekyna\Bundle\PaymentBundle\Model\PaymentTransitionTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class OrderController
 * @package Ekyna\Bundle\OrderBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderController extends ResourceController
{
    use PaymentTransitionTrait;

    /**
     * Payment transition action.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function paymentTransitionAction(Request $request)
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
