<?php

namespace Ekyna\Bundle\OrderBundle\Service;

use Doctrine\ORM\Query;
use Ekyna\Bundle\OrderBundle\Entity\OrderRepository;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderTypes;
use Payum\Core\Security\Util\Random;

/**
 * Class Generator
 * @package Ekyna\Bundle\OrderBundle\Service
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Generator implements GeneratorInterface
{
    /**
     * @var OrderRepository
     */
    private $repository;

    /**
     * Constructor.
     * 
     * @param OrderRepository $repository
     */
    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNumber(OrderInterface $order, $type = OrderTypes::TYPE_ORDER)
    {
        $date = new \DateTime();

    	$qb = $this->repository->createQueryBuilder('o');
    	$qb
            ->select('o.number')
    	    ->where($qb->expr()->eq('o.type', ':type'))
    	    ->andWhere($qb->expr()->eq('YEAR(o.createdAt)', ':year'))
    	    ->andWhere($qb->expr()->eq('MONTH(o.createdAt)', ':month'))
    	    ->andWhere($qb->expr()->neq('o.id', ':id'))
    	    ->andWhere($qb->expr()->isNotNull('o.number'))
    	    ->orderBy('o.number', 'DESC')
            ->setMaxResults(1)
    	    ->setParameter('type', OrderTypes::TYPE_ORDER)
    	    ->setParameter('year', $date->format('Y'))
    	    ->setParameter('month', $date->format('m'))
    	    ->setParameter('id', $order->getId())
    	;

    	if (null !== $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_SCALAR)) {
            $order->setNumber((string) (intval($result['number']) + 1));
    	} else {
            $order->setNumber($date->format('ym') . str_pad('1', 5, '0', STR_PAD_LEFT));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey(OrderInterface $order, $type = OrderTypes::TYPE_ORDER)
    {
        // TODO Why by type ? This may lead to conflict between orders and quotes ...

        $qb = $this->repository->createQueryBuilder('o');
        $query = $qb
            ->select('o.id')
            ->andWhere($qb->expr()->eq('o.type', ':type'))
            ->andWhere($qb->expr()->eq('o.key', ':key'))
            ->getQuery()
            ->setMaxResults(1)
        ;

        do {
            $key = substr(Random::generateToken(), 0, 32);
            $result = $query
                ->setParameter('key', $key)
                ->setParameter('type', $type)
                ->getOneOrNullResult(Query::HYDRATE_SCALAR)
            ;
        } while(null !== $result);

    	$order->setKey($key);
    }
}
