<?php

namespace Ekyna\Bundle\OrderBundle\Provider;

/**
 * Class ItemProviderRegistry
 * @package Ekyna\Bundle\OrderBundle\Provider
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ItemProviderRegistry implements ItemProviderRegistryInterface
{
    /**
     * @var array|ItemProviderInterface[]
     */
    protected $providers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->providers = [];
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider(ItemProviderInterface $provider)
    {
        if (array_key_exists($provider->getName(), $this->providers)) {
            throw new \RuntimeException(sprintf('Item provider "%s" is already registered.', $provider->getName()));
        }

        $this->providers[$provider->getName()] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviders()
    {
        return $this->providers;
    }
}
