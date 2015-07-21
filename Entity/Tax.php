<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Ekyna\Component\Sale\Tax\Tax as BaseTax;

/**
 * Class Tax
 * @package Ekyna\Bundle\OrderBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Tax extends BaseTax
{
    /**
     * @var integer
     */
    protected $id;


    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Returns the identifier.
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
