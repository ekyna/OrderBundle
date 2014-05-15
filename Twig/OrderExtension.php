<?php

namespace Ekyna\Bundle\OrderBundle\Twig;

/**
 * OrderExtension.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $documentLogo;

    /**
     * Constructor.
     * 
     * @param string $documentLogo
     */
    public function __construct($documentLogo)
    {
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
     * {@inheritDoc}
     */
    public function getName()
    {
    	return 'ekyna_order';
    }
}
