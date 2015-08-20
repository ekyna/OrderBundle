<?php

namespace Ekyna\Bundle\OrderBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Ekyna\Component\Sale\Order\OrderTypes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

        $response = new Response();
        $response->setLastModified($order->getUpdatedAt());
        if ($response->isNotModified($request)) {
            return $response;
        }

        $content = $this->renderView('EkynaOrderBundle:Document:invoice.html.twig', array(
            'order' => $order,
        ));

        $format = $request->attributes->get('_format', 'html');
        if ('html' === $format) {
            $response->setContent($content);
        } elseif ('pdf' === $format) {
            $response->setContent($this->get('knp_snappy.pdf')->getOutputFromHtml($content));
            $response->headers->add(array('Content-Type' => 'application/pdf'));
        } else {
            throw new NotFoundHttpException('Unsupported format.');
        }

        if ($request->query->get('_download', false)) {
            $filename = sprintf('order-%s.%s', $order->getNumber(), $format);
            $contentDisposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename
            );
            $response->headers->set('Content-Disposition', $contentDisposition);
        }

        return $response;
    }
}
