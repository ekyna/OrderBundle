<?php

namespace Ekyna\Bundle\OrderBundle\Model;

use Ekyna\Bundle\CoreBundle\Model\AbstractConstants;
use Ekyna\Component\Sale\Order\OrderTypes as Types;

/**
 * Class OrderTypes
 * @package Ekyna\Bundle\OrderBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
final class OrderTypes extends AbstractConstants
{
    /**
     * {@inheritdoc}
     */
    static public function getConfig()
    {
        $prefix = 'ekyna_order.order.type.';
        return array(
            Types::TYPE_CART  => array($prefix.Types::TYPE_CART),
            Types::TYPE_ORDER => array($prefix.Types::TYPE_ORDER),
            Types::TYPE_QUOTE => array($prefix.Types::TYPE_QUOTE),
        );
    }
}
