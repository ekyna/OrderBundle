<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * OrderItemsType.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemsType extends AbstractType
{
    public function getParent()
    {
        return 'bootstrap_collection';
    }

    public function getName()
    {
    	return 'ekyna_order_order_items';
    }
}
