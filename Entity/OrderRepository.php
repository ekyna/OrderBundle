<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepository;
use Ekyna\Component\Sale\Order\OrderTypes;

/**
 * Class OrderRepository
 * @package Ekyna\Bundle\OrderBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderRepository extends ResourceRepository
{
    /**
     * {@inheritdoc}
     * @return \Ekyna\Component\Sale\Order\OrderInterface
     */
    public function createNew($type = OrderTypes::TYPE_ORDER)
    {
        $order = parent::createNew();
        $order->setType($type);

        return $order;
    }

    /**
     * Finds the order by his number.
     *
     * @param string $number
     * @param string $type
     * @return null|object
     */
    public function findOneByNumber($number, $type = OrderTypes::TYPE_ORDER)
    {
        return $this->findOneBy([
            'number' => $number,
            'type'   => $type
        ]);
    }

    /**
     * Finds the order by his key.
     *
     * @param string $key
     * @param string $type
     * @return null|object
     */
    public function findOneByKey($key, $type = OrderTypes::TYPE_ORDER)
    {
        return $this->findOneBy([
            'key' => $key,
            'type'   => $type
        ]);
    }
}
