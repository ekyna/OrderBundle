<?php

namespace Ekyna\Bundle\OrderBundle\Generator;

use Ekyna\Bundle\OrderBundle\Entity\OrderRepository;
use Ekyna\Bundle\OrderBundle\Model\NumberGeneratorInterface;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderTypes;

/**
 * Class NumberGenerator
 * @package Ekyna\Bundle\OrderBundle\Generator
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class NumberGenerator implements NumberGeneratorInterface
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
    public function generate(OrderInterface $order, $type = OrderTypes::TYPE_ORDER)
    {
        $date = new \DateTime();

        // TODO select only date
    	$qb = $this->repository->createQueryBuilder('o');
    	$qb
    	   ->where($qb->expr()->eq('o.type', ':type'))
    	   ->andWhere($qb->expr()->eq('YEAR(o.createdAt)', ':year'))
    	   ->andWhere($qb->expr()->eq('MONTH(o.createdAt)', ':month'))
    	   ->andWhere($qb->expr()->neq('o.id', ':id'))
    	   ->andWhere($qb->expr()->isNotNull('o.number'))
    	   ->orderBy('o.number', 'DESC')
    	   ->setFirstResult(0)
    	   ->setMaxResults(1)
    	   ->setParameter('type', OrderTypes::TYPE_ORDER)
    	   ->setParameter('year', $date->format('Y'))
    	   ->setParameter('month', $date->format('m'))
    	   ->setParameter('id', $order->getId())
    	;

        // TODO single result
    	if (1 == count($results = $qb->getQuery()->getResult())) {
    	    return (string) (intval($results[0]->getNumber()) + 1);
    	}

    	return sprintf('%s%s', $date->format('ym'), str_pad('1', 5, '0', STR_PAD_LEFT));
    }
}
