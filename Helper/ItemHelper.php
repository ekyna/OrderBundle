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
        return $this->getProvider($subject)->transform($subject);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform(OrderItemInterface $item)
    {
        if ((null === $subject = $item->getSubject()) && null !== $item->getSubjectType()) {
            $subject = $this->getProvider($item)->reverseTransform($item);
            $item->setSubject($subject);
        }

        return $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions(OrderItemInterface $item, $property)
    {
        if (null !== $item->getSubjectType()) {
            return $this->getProvider($item)->getFormOptions($item, $property);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function generateFrontOfficePath($subjectOrOrderItem)
    {
        $provider = null;

        if ($subjectOrOrderItem instanceof OrderItemInterface && null !== $subjectOrOrderItem->getSubjectType()) {
            $provider = $this->getProvider($subjectOrOrderItem);
        } else {
            foreach ($this->registry->getProviders() as $p) {
                if ($p->supports($subjectOrOrderItem)) {
                    $provider = $p;
                    break;
                }
            }
        }

        if (null !== $provider) {
            return $provider->generateFrontOfficePath($subjectOrOrderItem);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function generateBackOfficePath($subjectOrOrderItem)
    {
        $provider = null;

        if ($subjectOrOrderItem instanceof OrderItemInterface && null !== $subjectOrOrderItem->getSubjectType()) {
            $provider = $this->getProvider($subjectOrOrderItem);
        } else {
            foreach ($this->registry->getProviders() as $p) {
                if ($p->supports($subjectOrOrderItem)) {
                    $provider = $p;
                    break;
                }
            }
        }

        if (null !== $provider) {
            return $provider->generateBackOfficePath($subjectOrOrderItem);
        }

        return null;
    }

    /**
     * Returns the provider supporting the subject or item.
     *
     * @param $subjectOrOrderItem
     * @return \Ekyna\Bundle\OrderBundle\Provider\ItemProviderInterface
     * @throws InvalidArgumentException
     */
    private function getProvider($subjectOrOrderItem)
    {
        foreach ($this->registry->getProviders() as $provider) {
            if ($provider->supports($subjectOrOrderItem)) {
                return $provider;
            }
        }

        throw new InvalidArgumentException('Unsupported subject.');
    }
}
