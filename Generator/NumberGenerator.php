<?php

namespace Ekyna\Bundle\OrderBundle\Generator;

use Ekyna\Bundle\OrderBundle\Entity\OrderRepository;
use Ekyna\Bundle\OrderBundle\Model\NumberGeneratorInterface;
use Ekyna\Component\Sale\Order\OrderInterface;

/**
 * NumberGenerator.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class NumberGenerator implements NumberGeneratorInterface
{
    /**
     * @var \Ekyna\Bundle\OrderBundle\Entity\OrderRepository
     */
    private $repository;

    /**
     * Constructor.
     * 
     * @param \Ekyna\Bundle\OrderBundle\Entity\OrderRepository $repository
     */
    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(OrderInterface $order, $type = OrderInterface::TYPE_ORDER)
    {
        $date = new \Datetime();

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
    	   ->setParameter('type', OrderInterface::TYPE_ORDER)
    	   ->setParameter('year', $date->format('Y'))
    	   ->setParameter('month', $date->format('m'))
    	   ->setParameter('id', $order->getId())
    	;

    	if (1 == count($results = $qb->getQuery()->getResult())) {
    	    return (string) intval($results[0]->getNumber()) + 1;
    	}

    	return sprintf('%s%s', $date->format('ym'), str_pad('1', 5, '0', STR_PAD_LEFT));
    }
}
