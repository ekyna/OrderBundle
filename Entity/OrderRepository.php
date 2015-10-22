<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepository;
use Ekyna\Bundle\OrderBundle\Helper\ItemHelperInterface;
use Ekyna\Bundle\UserBundle\Model\UserInterface;
use Ekyna\Component\Sale\Order\OrderTypes;

/**
 * Class OrderRepository
 * @package Ekyna\Bundle\OrderBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderRepository extends ResourceRepository
{
    /**
     * @var ItemHelperInterface
     */
    protected $itemHelper;


    /**
     * Sets the itemHelper.
     *
     * @param ItemHelperInterface $itemHelper
     */
    public function setItemHelper(ItemHelperInterface $itemHelper)
    {
        $this->itemHelper = $itemHelper;
    }

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
     * @return null|\Ekyna\Component\Sale\Order\OrderInterface
     */
    public function findOneByNumber($number, $type = OrderTypes::TYPE_ORDER)
    {
        return $this->findOneBy(array(
            'number' => $number,
            'type'   => $type
        ));
    }

    /**
     * Finds the order by his key.
     *
     * @param string $key
     * @param string $type
     * @return null|\Ekyna\Component\Sale\Order\OrderInterface
     */
    public function findOneByKey($key, $type = OrderTypes::TYPE_ORDER)
    {
        return $this->findOneBy(array(
            'key' => $key,
            'type'   => $type
        ));
    }

    /**
     * Finds orders by subject, optionally filtered by user and/or type (default order).
     *
     * @param object        $subject
     * @param UserInterface $user
     * @param string        $type
     * @param int           $hydrationMode
     * @return \Ekyna\Component\Sale\Order\OrderInterface[]
     */
    public function findBySubject(
        $subject,
        UserInterface $user = null,
        $type = OrderTypes::TYPE_ORDER,
        $hydrationMode = Query::HYDRATE_OBJECT
    ) {
        $item = $this->itemHelper->transform($subject);

        $qb = $this->getCollectionQueryBuilder();
        $qb
            ->join('o.items', 'i')
            ->andWhere($qb->expr()->eq('o.type', ':type'))
            ->andWhere($qb->expr()->eq('i.subjectData', ':subject_data'))
            ->andWhere($qb->expr()->eq('i.subjectType', ':subject_type'))
            ->addOrderBy('o.updatedAt', 'desc')
            ->groupBy('o.id')
        ;
        if (null !== $user) {
            $qb->andWhere($qb->expr()->eq('o.user', ':user'));
        }

        $query = $qb
            ->getQuery()
            ->setParameter('type', $type)
            ->setParameter('subject_type', $item->getSubjectType())
            ->setParameter('subject_data', $item->getSubjectData(), Type::JSON_ARRAY)
        ;
        if (null !== $user) {
            $query->setParameter('user', $user);
        }

        return $query->getResult($hydrationMode);
    }

    /**
     * Returns the alias.
     *
     * @return string
     */
    protected function getAlias()
    {
        return 'o';
    }
}
