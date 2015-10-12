<?php

namespace Ekyna\Bundle\OrderBundle\Payum\Action\Paypal;

use Ekyna\Bundle\OrderBundle\Service\CalculatorInterface;
use Ekyna\Component\Sale\Order\OrderPaymentInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use Payum\Paypal\ExpressCheckout\Nvp\Action\ConvertPaymentAction as BaseAction;

/**
 * Class ConvertPaymentAction
 * @package Ekyna\Bundle\OrderBundle\Payum\Action\Paypal
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ConvertPaymentAction extends BaseAction
{
    /**
     * @var CalculatorInterface
     */
    protected $calculator;


    /**
     * Constructor.
     * @param CalculatorInterface $calculator
     */
    public function __construct(CalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        parent::execute($request);

        /** @var OrderPaymentInterface $payment */
        $payment = $request->getSource();
        /** @var array $model */
        $model = $request->getResult();

        $model['NOSHIPPING'] = 1;
        $model['LANDINGPAGE'] = 'Billing';
        $model['SOLUTIONTYPE'] = 'Sole';

        $order = $payment->getOrder();
        $orderTotal = $this->calculator->calculateOrderTotal($order, true);
        $paymentAmount = $payment->getAmount();

        // Add item details only if this is a full order payement.
        if (0 == bccomp($orderTotal, $paymentAmount, 2)) {
            $m = 0;
            foreach($order->getItems() as $item) {
                $model['L_PAYMENTREQUEST_0_NAME'.$m] = $item->getDesignation();
                $model['L_PAYMENTREQUEST_0_AMT'.$m] = round($item->getPrice(), 2);
                $model['L_PAYMENTREQUEST_0_QTY'.$m] = $item->getQuantity();
                $m++;
            }
            $model['PAYMENTREQUEST_0_ITEMAMT'] = round($this->calculator->calculateOrderItemsTotal($order), 2);

            $model['PAYMENTREQUEST_0_SHIPPINGAMT'] = round($this->calculator->calculateOrderShipmentTotal($order), 2);

            $taxes = $this->calculator->calculateOrderTaxesAmounts($order);
            $model['PAYMENTREQUEST_0_TAXAMT'] = round($taxes->getTotal(), 2);
        }

        $request->setResult((array) $model);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof OrderPaymentInterface &&
            $request->getTo() == 'array'
        ;
    }
}
