<?php

namespace Ekyna\Bundle\OrderBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Ekyna\Component\Sale\Order\OrderTypes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DocumentController
 * @package Ekyna\Bundle\OrderBundle\Controller
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class DocumentController extends Controller
{
    /**
     * Invoice (render) action.
     *
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function invoiceAction(Request $request)
    {
        /** @var \Ekyna\Component\Sale\Order\OrderInterface $order */
        $order = $this->get('ekyna_order.order.repository')->findOneBy(array(
            'id' => $request->attributes->get('orderId', null), 
            'type' => OrderTypes::TYPE_ORDER
        ));
        if (null === $order) {
            throw new NotFoundHttpException('Order not found.');
        }

        // Admin or owner security check
        if(!($this->get('security.context')->isGranted('ROLE_ADMIN') || $order->getUser() == $this->getUser())) {
            throw new AccessDeniedHttpException('You are not allowed to view this resource.');
        }

        $content = $this->renderView('EkynaOrderBundle:Document:invoice.html.twig', array(
            'order' => $order,
        ));

        $format = $request->attributes->get('_format', 'html');
        $headers = array(
            'Content-Type' => 'text/html',
        );

        if ('pdf' == $format) {
            $content = $this->get('knp_snappy.pdf')->getOutputFromHtml($content);
            $headers['Content-Type'] = 'application/pdf';
        }
        /* elseif ('jpg' == $format) {
            $content = $this->get('knp_snappy.image')->getOutputFromHtml($content);
            $headers['Content-Type'] = 'image/jpg';
        }*/

        if ($request->attributes->get('_download', false)) {
            $headers['Content-Disposition'] = sprintf('attachment; filename="order-%s.%s"', $order->getNumber(), $format);
        }

        return new Response($content, 200, $headers);
    }
}
