<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepository;
use Ekyna\Component\Sale\Order\OrderStatuses;

/**
 * OrderRepository.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderRepository extends ResourceRepository
{
    public function findCart($cartId)
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->andWhere($qb->expr()->eq('c.id', ':id'))
            ->andWhere($qb->expr()->eq('c.status', OrderStatuses::CART))
            ->setParameter('id', $cartId)
        ;

        return $qb->getQuery()->getSingleResult();
    }
}
