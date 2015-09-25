<?php

namespace Ekyna\Bundle\OrderBundle\Helper;

use Ekyna\Bundle\OrderBundle\Exception\InvalidArgumentException;
use Ekyna\Component\Sale\Order\OrderItemInterface;

/**
 * Interface ItemHelperInterface
 * @package Ekyna\Bundle\OrderBundle\Helper
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ItemHelperInterface
{
    /**
     * Transform the subject to an order item.
     *
     * @param object $subject
     * @return OrderItemInterface
     * @throws InvalidArgumentException
     */
    public function transform($subject);

    /**
     * Returns the subject from the order item.
     *
     * @param OrderItemInterface $item
     * @return object|null
     * @throws InvalidArgumentException
     */
    public function reverseTransform(OrderItemInterface $item);

    /**
     * Returns the order item form options.
     *
     * @param OrderItemInterface $item
     * @param string             $property
     * @return array
     * @throws InvalidArgumentException
     */
    public function getFormOptions(OrderItemInterface $item, $property);

    /**
     * Generates the front office path for the given subject or order item.
     *
     * @param object $subjectOrOrderItem
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function generateFrontOfficePath($subjectOrOrderItem);

    /**
     * Generates the back office path for the given subject or order item.
     *
     * @param object $subjectOrOrderItem
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function generateBackOfficePath($subjectOrOrderItem);
}
