<?php

namespace Ekyna\Bundle\OrderBundle\Service;

use SM\StateMachine\StateMachineInterface as BaseStateMachineInterface;

/**
 * Interface StateMachineInterface
 * @package Ekyna\Bundle\OrderBundle\Service
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface StateMachineInterface extends BaseStateMachineInterface
{
    /**
     * Returns the possible transition from given state
     * Returns null if no transition is possible
     *
     * @param string $fromState
     *
     * @return string|null
     */
    public function getTransitionFromState($fromState);

    /**
     * Returns the possible transition to the given state
     * Returns null if no transition is possible
     *
     * @param string $toState
     *
     * @return string|null
     */
    public function getTransitionToState($toState);
}
