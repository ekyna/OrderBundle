<?php

namespace Ekyna\Bundle\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Ekyna\Component\Sale\Order\OrderInterface;

class DocumentController extends Controller
{
    public function invoiceAction(Request $request)
    {
        $order = $this->get('ekyna_order.order.repository')->findOneBy(array(
            'id' => $request->attributes->get('orderId', null), 
            'type' => OrderInterface::TYPE_ORDER
        ));
        if (null === $order) {
            throw new NotFoundHttpException('Order not found.');
        }

        $content = $this->renderView(
            'EkynaOrderBundle:Order:invoice.html.twig',
            array(
        	    'order' => $order,
            )
        );

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
