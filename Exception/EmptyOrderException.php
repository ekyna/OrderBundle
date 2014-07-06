<?php

namespace Ekyna\Bundle\OrderBundle\Exception;

/**
 * EmptyOrderException.
 *
 * @author Etienne Dauvergne <contact@ekyna.com>
 */
class EmptyOrderException extends OrderException
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        if (null === $message) {
            $message = 'Order is empty.';
        }
        parent::__construct($message, $code, $previous);
    }
}
