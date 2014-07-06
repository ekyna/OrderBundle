<?php

namespace Ekyna\Bundle\OrderBundle\Exception;

/**
 * LockedOrderException.
 *
 * @author Etienne Dauvergne <contact@ekyna.com>
 */
class LockedOrderException extends OrderException
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        if (null === $message) {
            $message = 'Order is locked.';
        }
        parent::__construct($message, $code, $previous);
    }
}
