<?php

namespace Ekyna\Bundle\OrderBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Order
 * @package Ekyna\Bundle\OrderBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Order extends Constraint
{
    public $userIsMandatory = 'ekyna_order.order.user_is_mandatory';
    public $invoiceAddressIsMandatory = 'ekyna_order.order.invoice_address_is_mandatory';
    public $deliveryAddressIsMandatory = 'ekyna_order.order.delivery_address_is_mandatory';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
} 