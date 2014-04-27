<?php

namespace Ekyna\Bundle\OrderBundle\Updater;

use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Bundle\OrderBundle\Model\UpdaterInterface;

/**
 * OrderUpdater.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderUpdater implements UpdaterInterface
{
    /**
     * Updates the order totals
     * 
     * @param \Ekyna\Component\Sale\Order\OrderInterface $order
     */
    public function update(OrderInterface $order)
    {
        $netTotal = $atiTotal = $totalWeight = $itemsCount = 0;
        
        foreach ($order->getItems() as $item) {
            $netTotal += $item->getTotalNetPrice();
            $atiTotal += $item->getTotalAtiPrice();
            $itemsCount += $item->getQuantity();
            $totalWeight += $item->getTotalWeight();
        }

        // TODO: Adjustments / Shipping

        $order
            ->setItemsCount($itemsCount)
            ->setTotalWeight($totalWeight)
            ->setNetTotal($netTotal)
            ->setAtiTotal(round($atiTotal, 2))
        ;
    }
}
