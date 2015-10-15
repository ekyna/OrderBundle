<?php

namespace Ekyna\Bundle\OrderBundle\Twig;

use Ekyna\Bundle\OrderBundle\Model\OrderStates;
use Ekyna\Bundle\OrderBundle\Service\CalculatorInterface;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class OrderExtension
 * @package Ekyna\Bundle\OrderBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var string
     */
    private $documentLogo;


    /**
     * Constructor.
     * 
     * @param TranslatorInterface $translator
     * @param CalculatorInterface $calculator
     * @param string              $documentLogo
     */
    public function __construct(
        TranslatorInterface $translator,
        CalculatorInterface $calculator,
        $documentLogo
    ) {
        $this->translator = $translator;
        $this->calculator   = $calculator;
        $this->documentLogo = $documentLogo;
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobals()
    {
        return array(
        	'order_document_logo' => $this->documentLogo,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('order_total',       array($this, 'getOrderTotal')),
            new \Twig_SimpleFilter('order_item_total',  array($this, 'getOrderItemTotal')),
            new \Twig_SimpleFilter('order_taxes',       array($this, 'getOrderTaxes')),
            new \Twig_SimpleFilter('order_item_tax',    array($this, 'getOrderItemTax')),

            new \Twig_SimpleFilter('order_state_label', array($this, 'getOrderStateLabel'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('order_state_badge', array($this, 'getOrderStateBadge'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns the order state label.
     *
     * @param string|OrderInterface $stateOrOrder $state
     * @return string
     */
    public function getOrderStateLabel($stateOrOrder)
    {
        $state = $stateOrOrder instanceof OrderInterface ? $stateOrOrder->getState() : $stateOrOrder;

        return $this->translator->trans(OrderStates::getLabel($state));
    }

    /**
     * Returns the order state badge.
     *
     * @param string|OrderInterface $stateOrOrder
     * @return string
     */
    public function getOrderStateBadge($stateOrOrder)
    {
        $state = $stateOrOrder instanceof OrderInterface ? $stateOrOrder->getState() : $stateOrOrder;

        return sprintf(
            '<span class="label label-%s">%s</span>',
            OrderStates::getTheme($state),
            $this->getOrderStateLabel($state)
        );
    }
    
    /**
     * Returns the order total.
     *
     * @param OrderInterface $order
     * @param bool           $ati
     *
     * @return float
     */
    public function getOrderTotal(OrderInterface $order, $ati = false)
    {
        return $this->calculator->calculateOrderTotal($order, $ati);
    }

    /**
     * Returns the order item total.
     *
     * @param OrderItemInterface $item
     * @param bool               $ati
     *
     * @return float
     */
    public function getOrderItemTotal(OrderItemInterface $item, $ati = false)
    {
        return $this->calculator->calculateOrderItemTotal($item, $ati);
    }

    /**
     * Returns the order taxes.
     *
     * @param OrderInterface $order
     *
     * @return \Ekyna\Component\Sale\Tax\TaxesAmounts
     */
    public function getOrderTaxes(OrderInterface $order)
    {
        return $this->calculator->calculateOrderTaxesAmounts($order);
    }

    /**
     * Returns the order item tax.
     *
     * @param OrderItemInterface $item
     *
     * @return \Ekyna\Component\Sale\Tax\TaxAmount
     */
    public function getOrderItemTax(OrderItemInterface $item)
    {
        return $this->calculator->calculateOrderItemTaxAmount($item);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
    	return 'ekyna_order';
    }
}
