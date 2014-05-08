<?php

namespace Ekyna\Bundle\OrderBundle\Payum\Action\Paypal;

use Ekyna\Component\Sale\Order\OrderPaymentInterface;
use Payum\Bundle\PayumBundle\Security\TokenFactory;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\SecuredCaptureRequest;

/**
 * CaptureOrderUsingExpressCheckoutAction.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CaptureOrderUsingExpressCheckoutAction extends PaymentAwareAction
{
    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * @param TokenFactory $tokenFactory
     */
    public function __construct(TokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    public function execute($request)
    {
        /** @var $request \Payum\Core\Request\SecuredCaptureRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var \Ekyna\Component\Sale\Order\OrderPaymentInterface $payment */
        $payment = $request->getModel();

        $details = $payment->getDetails();
        if (empty($details)) {
            /** @var \Ekyna\Component\Sale\Order\OrderInterface $order */
            $order = $payment->getOrder();
            $token = $request->getToken();

            $details['INVNUM'] = $payment->getId();
            $details['RETURNURL'] = $token->getTargetUrl();
            $details['CANCELURL'] = $token->getTargetUrl();
            $details['NOSHIPPING'] = 1;
            $details['LANDINGPAGE'] = 'Billing';
            $details['SOLUTIONTYPE'] = 'Sole';
            /*$details['PAYMENTREQUEST_0_NOTIFYURL'] = $this->tokenFactory->createNotifyToken(
                $request->getToken()->getPaymentName(),
                $order
            )->getTargetUrl();*/

            $details['PAYMENTREQUEST_n_INVNUM'] = $payment->getId();
            $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'EUR';
            $details['PAYMENTREQUEST_0_AMT'] = $order->getAtiTotal();

            $m = $itemTotal = 0;

            foreach($order->getItems() as $item) {
                $details['L_PAYMENTREQUEST_n_NAME'.$m] = $item->getDesignation();
                $details['L_PAYMENTREQUEST_n_AMT'.$m] = round($item->getBaseAtiPrice(), 2);
                $details['L_PAYMENTREQUEST_n_QTY'.$m] = $item->getQuantity();
                $itemTotal += $item->getTotalAtiPrice();
                $m++;
            }

            $details['PAYMENTREQUEST_0_ITEMAMT'] = round($itemTotal, 2);

            //$details['PAYMENTREQUEST_0_SHIPPINGAMT'] = $order->get();

            $details['PAYMENTREQUEST_n_TAXAMT'] = round($order->getAtiTotal() - $order->getNetTotal(), 2);

            $payment->setDetails($details);
        }

        try {
            $request->setModel($payment->getDetails());

            $this->payment->execute($request);

            $payment->setDetails((array) $request->getModel());
            $request->setModel($payment);
        } catch (\Exception $e) {
            $payment->setDetails((array) $request->getModel());
            $request->setModel($payment);

            throw $e;
        }
    }

    public function supports($request)
    {
        return
            $request instanceof SecuredCaptureRequest &&
            $request->getModel() instanceof OrderPaymentInterface
        ;
    }
}
