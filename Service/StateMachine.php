<?php

namespace Ekyna\Bundle\OrderBundle\Service;

use SM\StateMachine\StateMachine as BaseStateMachine;

/**
 * Class StateMachine
 * @package Ekyna\Bundle\OrderBundle\Service
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class StateMachine extends BaseStateMachine implements StateMachineInterface
{
    /**
     * {@inheritDoc}
     */
    public function getTransitionFromState($fromState)
    {
        foreach ($this->getPossibleTransitions() as $transition) {
            $config = $this->config['transitions'][$transition];
            if (in_array($fromState, $config['from'])) {
                return $transition;
            }
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getTransitionToState($toState)
    {
        foreach ($this->getPossibleTransitions() as $transition) {
            $config = $this->config['transitions'][$transition];
            if ($toState === $config['to']) {
                return $transition;
            }
        }
        return null;
    }
}
