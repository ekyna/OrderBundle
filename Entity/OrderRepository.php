<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepository;
use Ekyna\Component\Sale\Order\OrderInterface;

/**
 * OrderRepository.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderRepository extends ResourceRepository
{
    public function createNew($type = OrderInterface::TYPE_ORDER)
    {
        $order = parent::createNew();
        $order->setType($type);

        return $order;
    }
}
