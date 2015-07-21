<?php

namespace Ekyna\Bundle\OrderBundle\Exception;

/**
 * Class InvalidItemException
 * @package Ekyna\Bundle\OrderBundle\Exception
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class InvalidItemException extends OrderException
{
    /**
     * @param string $message
     */
    public function __construct($message = 'Unsupported order item')
    {
        $this->message = $message;
    }
}
