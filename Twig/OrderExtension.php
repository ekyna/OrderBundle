<?php

namespace Ekyna\Bundle\OrderBundle\Twig;

use Ekyna\Bundle\OrderBundle\Service\CalculatorInterface;
use Ekyna\Component\Sale\Order\OrderInterface;
use Ekyna\Component\Sale\Order\OrderItemInterface;

/**
 * Class OrderExtension
 * @package Ekyna\Bundle\OrderBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderExtension extends \Twig_Extension
{
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
     * @param CalculatorInterface $calculator
     * @param string              $documentLogo
     */
    public function __construct(CalculatorInterface $calculator, $documentLogo)
    {
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
            new \Twig_SimpleFilter('total', array($this, 'totalFilter')),
            new \Twig_SimpleFilter('taxes', array($this, 'taxesFilter')),
            new \Twig_SimpleFilter('price', array($this, 'priceFilter'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns the total.
     *
     * @param mixed $input
     * @param bool $ati
     *
     * @return float
     */
    public function totalFilter($input, $ati = false)
    {
        if ($input instanceof OrderItemInterface) {
            return $this->calculator->calculateOrderItemTotal($input, $ati);
        } elseif ($input instanceof OrderInterface) {
            return $this->calculator->calculateOrderTotal($input, $ati);
        } else {
            throw new \InvalidArgumentException('Expected OrderItemInterface or OrderInterface');
        }
    }

    /**
     * Returns the taxes.
     *
     * @param mixed $input
     *
     * @return \Ekyna\Component\Sale\Tax\TaxesAmounts
     */
    public function taxesFilter($input)
    {
        if ($input instanceof OrderItemInterface) {
            throw new \RuntimeException('Not yet implemented.');
        } elseif ($input instanceof OrderInterface) {
            return $this->calculator->calculateOrderTaxesAmounts($input);
        } else {
            throw new \InvalidArgumentException('Expected OrderItemInterface or OrderInterface');
        }
    }

    /**
     * Returns a formatted price
     *
     * @param float $price
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     *
     * @return string
     */
    public function priceFilter($price, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        if (!is_numeric($price)) {
            throw new \InvalidArgumentException('Expected numeric value.');
        }

        $price = number_format($price, $decimals, $decPoint, $thousandsSep);
        $price = $price . '&nbsp;€';

        return $price;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
    	return 'ekyna_order';
    }
}
