<?php

namespace Ekyna\Bundle\OrderBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrderController
 * @package Ekyna\Bundle\OrderBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderController extends ResourceController
{
    public function addItemAction(Request $request)
    {
        throw new \Exception('Not implemented.'); 
    }
}
