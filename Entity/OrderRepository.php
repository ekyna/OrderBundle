<?php

namespace Ekyna\Bundle\OrderBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepository;
use Ekyna\Bundle\OrderBundle\Helper\ItemHelperInterface;
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
     * Finds orders by subject.
     *
     * @param object $subject
     * @param string $type
     * @param int    $hydrationMode
     * @return \Ekyna\Component\Sale\Order\OrderInterface[]
     */
    public function findBySubject($subject, $type = OrderTypes::TYPE_ORDER, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        $item = $this->itemHelper->transform($subject);

        $dql = <<<DQL
SELECT o FROM Ekyna\Bundle\OrderBundle\Entity\Order o
JOIN o.items AS i
WHERE o.type = :type
  AND i.subjectData = :subject_data
  AND i.subjectType = :subject_type
GROUP BY o.id
DQL;

        $query = $this->getEntityManager()->createQuery($dql);

        return $query
            ->setParameter('type', $type)
            ->setParameter('subject_type', $item->getSubjectType())
            ->setParameter('subject_data', $item->getSubjectData(), Type::JSON_ARRAY)
            ->getResult($hydrationMode)
        ;
    }
}
