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
        return array(
            States::STATE_NEW       => array($prefix.States::STATE_NEW,       'default', false),
            States::STATE_PENDING   => array($prefix.States::STATE_PENDING,   'warning', true),
            States::STATE_REFUSED   => array($prefix.States::STATE_REFUSED,   'danger',  false),
            States::STATE_ACCEPTED  => array($prefix.States::STATE_ACCEPTED,  'success', true),
            States::STATE_COMPLETED => array($prefix.States::STATE_COMPLETED, 'success', true),
            States::STATE_REFUNDED  => array($prefix.States::STATE_REFUNDED,  'primary', true),
            States::STATE_CANCELLED => array($prefix.States::STATE_CANCELLED, 'default', false),
        );
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
