<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class OrderItemsType
 * @package Ekyna\Bundle\OrderBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemsType extends AbstractType
{
    public function getParent()
    {
        return 'ekyna_core_collection';
    }

    public function getName()
    {
    	return 'ekyna_order_order_items';
    }
}
