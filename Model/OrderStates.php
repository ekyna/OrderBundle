<?php

namespace Ekyna\Bundle\OrderBundle\Model;

use Ekyna\Bundle\CoreBundle\Model\AbstractConstants;
use Ekyna\Component\Sale\Order\OrderStates as States;

/**
 * Class OrderStates
 * @package Ekyna\Bundle\OrderBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
final class OrderStates extends AbstractConstants
{
    /**
     * {@inheritdoc}
     */
    static public function getConfig()
    {
        $prefix = 'ekyna_order.order.state.';
        return [
            States::STATE_NEW       => [$prefix.States::STATE_NEW,       'default'],
            States::STATE_PENDING   => [$prefix.States::STATE_PENDING,   'warning'],
            States::STATE_REFUSED   => [$prefix.States::STATE_REFUSED,   'danger'],
            States::STATE_ACCEPTED  => [$prefix.States::STATE_ACCEPTED,  'success'],
            States::STATE_COMPLETED => [$prefix.States::STATE_COMPLETED, 'success'],
            States::STATE_REFUNDED  => [$prefix.States::STATE_REFUNDED,  'primary'],
            States::STATE_CANCELLED => [$prefix.States::STATE_CANCELLED, 'default'],
        ];
    }

    /**
     * Returns the theme for the given state.
     *
     * @param string $state
     * @return string
     */
    static public function getTheme($state)
    {
        static::isValid($state, true);

        return static::getConfig()[$state][1];
    }
}
