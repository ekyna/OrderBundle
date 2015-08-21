<?php

namespace Ekyna\Bundle\OrderBundle\Provider;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractItemProvider
 * @package Ekyna\Bundle\OrderBundle\Provider
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractItemProvider implements ItemProviderInterface
{
    /**
     * @var string
     */
    protected $orderItemClass;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;


    /**
     * Sets the order item class.
     *
     * @param string $orderItemClass
     * @return AbstractItemProvider
     */
    public function setOrderItemClass($orderItemClass)
    {
        $this->orderItemClass = $orderItemClass;
        return $this;
    }

    /**
     * Sets the urlGenerator.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @return AbstractItemProvider
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        return $this;
    }

    /**
     * Creates a new order item.
     *
     * @return \Ekyna\Component\Sale\Order\OrderItemInterface
     */
    protected function createNewOrderItem()
    {
        return new $this->orderItemClass;
    }
}
