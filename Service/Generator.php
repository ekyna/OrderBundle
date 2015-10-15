<?php

namespace Ekyna\Bundle\OrderBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderTypes;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Payum\Core\Security\Util\Random;

/**
 * Class Generator
 * @package Ekyna\Bundle\OrderBundle\Service
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Generator implements GeneratorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var string
     */
    private $orderClass;

    /**
     * @var array
     */
    private $disabledEventListeners;


    /**
     * Constructor.
     * 
     * @param EntityManagerInterface $manager
     * @param string                 $orderClass
     */
    public function __construct(EntityManagerInterface $manager, $orderClass)
    {
        $this->manager = $manager;
        $this->orderClass = $orderClass;
    }

    /**
     * {@inheritdoc}
     */
    public function generateNumber(OrderInterface $order, $type = OrderTypes::TYPE_ORDER)
    {
        if (null !== $order->getNumber()) {
            return $this;
        }

        $this->manager->getFilters()->disable('softdeleteable');

        if (null === $date = $order->getCreatedAt()) {
            $order->setCreatedAt($date = new \DateTime());
        }

        if (null !== $order->getId()) {
            $selectDql = <<<DQL
SELECT o.number
FROM $this->orderClass o
WHERE o.type = :type
  AND YEAR(o.createdAt) = :year
  AND MONTH(o.createdAt) = :month
  AND o.number IS NOT NULL
  AND o.id != :id
ORDER BY o.number DESC
DQL;
        } else {
            $selectDql = <<<DQL
SELECT o.number
FROM $this->orderClass o
WHERE o.type = :type
  AND YEAR(o.createdAt) = :year
  AND MONTH(o.createdAt) = :month
  AND o.number IS NOT NULL
ORDER BY o.number DESC
DQL;
        }

        $query = $this->manager->createQuery($selectDql);
        $query
            ->setMaxResults(1)
            ->setParameter('type', $order->getType())
            ->setParameter('year', $date->format('Y'))
            ->setParameter('month', $date->format('m'))
        ;

        if (null !== $order->getId()) {
            $query->setParameter('id', $order->getId());
        }

    	if (null !== $result = $query->getOneOrNullResult(Query::HYDRATE_SCALAR)) {
            $order->setNumber((string) (intval($result['number']) + 1));
    	} else {
            $order->setNumber($date->format('ym') . str_pad('1', 5, '0', STR_PAD_LEFT));
        }

        $this->manager->getFilters()->enable('softdeleteable');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey(OrderInterface $order)
    {
        if (null !== $order->getKey()) {
            return $this;
        }

        $this->manager->getFilters()->disable('softdeleteable');

        $query = $this->manager->createQuery(<<<DQL
SELECT o.id
FROM $this->orderClass o
WHERE o.key = :key
DQL
);
        $query->setMaxResults(1);

        do {
            $key = substr(preg_replace('~[^a-zA-Z\d]~', '', Random::generateToken()), 0, 32);
            $result = $query
                ->setParameter('key', $key)
                ->getOneOrNullResult(Query::HYDRATE_SCALAR)
            ;
        } while(null !== $result);

    	$order->setKey($key);

        $this->manager->getFilters()->enable('softdeleteable');

        return $this;
    }

    /**
     * Disable the soft deletable listener.
     */
    private function disableSoftDeletable()
    {
        $this->disabledEventListeners = [];
        $eventManager = $this->manager->getEventManager();
        foreach ($eventManager->getListeners() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof SoftDeleteableListener) {
                    $eventManager->removeEventListener($eventName, $listener);
                    $this->disabledEventListeners[$eventName] = $listener;
                }
            }
        }
    }

    /**
     * Enable the previously disabled soft deletable listener.
     */
    private function enableSoftDeletable()
    {
        if (!empty($this->disabledEventListeners)) {
            $eventManager = $this->manager->getEventManager();
            foreach($this->disabledEventListeners as $eventName => $listener) {
                $eventManager->addEventListener($eventName, $listener);
            }
        }
        $this->disabledEventListeners = [];
    }
}
