<?php

namespace Ekyna\Bundle\OrderBundle\Payum\Action\Paypal;

use Ekyna\Bundle\OrderBundle\Entity\OrderPayment;
use Ekyna\Bundle\PaymentBundle\Payum\Action\AbstractCapturePaymentAction;
use Ekyna\Component\Sale\Payment\PaymentInterface;
use Payum\Core\Security\TokenInterface;

/**
 * Class CaptureOrderUsingExpressCheckoutAction
 * @package Ekyna\Bundle\OrderBundle\Payum\Action\Paypal
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CaptureOrderUsingExpressCheckoutAction extends AbstractCapturePaymentAction
{
    /**
     * {@inheritdoc}
     *
     * @param OrderPayment $payment
     */
    protected function composeDetails(PaymentInterface $payment, TokenInterface $token)
    {
        $details = $payment->getDetails();
        $order = $payment->getOrder();

        if (array_key_exists('PAYMENTREQUEST_0_INVNUM', $details)) {
            return;
        }

        $invNum = $payment->getCreatedAt()->format('ymd').$payment->getId();

        $details['INVNUM'] = $invNum;
        $details['NOSHIPPING'] = 1;
        $details['LANDINGPAGE'] = 'Billing';
        $details['SOLUTIONTYPE'] = 'Sole';

        $details['PAYMENTREQUEST_0_INVNUM'] = uniqid().'-'.$payment->getId();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = $payment->getCurrency();
        $details['PAYMENTREQUEST_0_AMT'] = round($payment->getAmount(), 2);
        $details['PAYMENTREQUEST_0_ITEMAMT'] = round($payment->getAmount(), 2);

        $m = $itemTotal = 0;

        foreach($order->getItems() as $item) {
            $details['L_PAYMENTREQUEST_0_NAME'.$m] = $item->getDesignation();
            $details['L_PAYMENTREQUEST_0_AMT'.$m] = round($item->getBaseNetPrice(), 2);
            $details['L_PAYMENTREQUEST_0_QTY'.$m] = $item->getQuantity();
            $itemTotal += $item->getTotalNetPrice();
            $m++;
        }

        $details['PAYMENTREQUEST_0_ITEMAMT'] = round($itemTotal, 2);

        $details['PAYMENTREQUEST_0_SHIPPINGAMT'] = $order->getNetShippingCost();

        $details['PAYMENTREQUEST_0_TAXAMT'] = round($order->getAtiTotal() - $order->getNetTotal(), 2);

        $payment->setDetails($details);
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsPayment($payment)
    {
        return $payment instanceof OrderPayment;
    }
}
