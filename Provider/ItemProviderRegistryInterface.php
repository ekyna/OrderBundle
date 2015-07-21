<?php

namespace Ekyna\Bundle\OrderBundle\Provider;

/**
 * Interface ItemProviderRegistryInterface
 * @package Ekyna\Bundle\OrderBundle\Provider
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ItemProviderRegistryInterface
{
    /**
     * Adds the item provider.
     *
     * @param ItemProviderInterface $provider
     */
    public function addProvider(ItemProviderInterface $provider);

    /**
     * Returns the providers.
     *
     * @return array|ItemProviderInterface[]
     */
    public function getProviders();
}
