<?php

namespace Ekyna\Bundle\OrderBundle\Exception;

/**
 * Class InvalidSubjectException
 * @package Ekyna\Bundle\OrderBundle\Exception
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class InvalidSubjectException extends OrderException
{
    /**
     * @param string $expectedClass
     */
    public function __construct($expectedClass)
    {
        $this->message = sprintf('Expected instance of "%s".', $expectedClass);
    }
}
