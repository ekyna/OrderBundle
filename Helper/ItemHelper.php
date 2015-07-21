<?php

namespace Ekyna\Bundle\OrderBundle\Helper;

use Ekyna\Bundle\OrderBundle\Exception\InvalidArgumentException;
use Ekyna\Bundle\OrderBundle\Provider\ItemProviderRegistry;
use Ekyna\Component\Sale\Order\OrderItemInterface;

/**
 * Class ItemHelper
 * @package Ekyna\Bundle\OrderBundle\Helper
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ItemHelper implements ItemHelperInterface
{
    /**
     * @var ItemProviderRegistry
     */
    protected $registry;


    /**
     * Constructor.
     *
     * @param ItemProviderRegistry $registry
     */
    public function __construct(ItemProviderRegistry $registry)
    {
        $this->registry = $registry;
    }


    /**
     * {@inheritdoc}
     */
    public function transform($subject)
    {
        foreach ($this->registry->getProviders() as $provider) {
            if ($provider->supports($subject)) {
                return $provider->transform($subject);
            }
        }

        throw new InvalidArgumentException('Unsupported subject.');
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform(OrderItemInterface $item)
    {
        if (null !== $subject = $item->getSubject()) {
            return $subject;
        }

        foreach ($this->registry->getProviders() as $provider) {
            if ($provider->supports($item)) {
                $subject = $provider->reverseTransform($item);
                $item->setSubject($subject);
                return $subject;
            }
        }

        throw new InvalidArgumentException('Unsupported subject.');
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions(OrderItemInterface $item, $property)
    {
        foreach ($this->registry->getProviders() as $provider) {
            if ($provider->supports($item)) {
                return $provider->getFormOptions($item, $property);
            }
        }

        throw new InvalidArgumentException('Unsupported subject.');
    }

    /**
     * {@inheritdoc}
     */
    public function generateFrontOfficePath($subjectOrOrderItem)
    {
        foreach ($this->registry->getProviders() as $provider) {
            if ($provider->supports($subjectOrOrderItem)) {
                return $provider->generateFrontOfficePath($subjectOrOrderItem);
            }
        }

        throw new InvalidArgumentException('Unsupported subject.');
    }

    /**
     * {@inheritdoc}
     */
    public function generateBackOfficePath($subjectOrOrderItem)
    {
        foreach ($this->registry->getProviders() as $provider) {
            if ($provider->supports($subjectOrOrderItem)) {
                return $provider->generateBackOfficePath($subjectOrOrderItem);
            }
        }

        throw new InvalidArgumentException('Unsupported subject.');
    }
}
