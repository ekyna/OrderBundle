<?php

namespace Ekyna\Bundle\OrderBundle\Validator\Constraints;

use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderTypes;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class OrderValidator
 * @package Ekyna\Bundle\OrderBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($order, Constraint $constraint)
    {
        /* @var OrderInterface $order */
        /* @var Order $constraint */

        if (null === $order->getInvoiceAddress()) {
            $this->context->addViolationAt(
                'invoiceAddress',
                $constraint->invoiceAddressIsMandatory
            );
        }
        if ($order->requiresShipment() && (!$order->getSameAddress() && null === $order->getDeliveryAddress())) {
            $this->context->addViolationAt(
                'deliveryAddress',
                $constraint->deliveryAddressIsMandatory
            );
        }
    }
}
