<?php

namespace Ekyna\Bundle\OrderBundle\Dashboard;

use Ekyna\Bundle\AdminBundle\Dashboard\Widget\Type\AbstractWidgetType;
use Ekyna\Bundle\AdminBundle\Dashboard\Widget\WidgetInterface;
use Ekyna\Bundle\OrderBundle\Entity\OrderRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrdersWidgetType
 * @package Ekyna\Bundle\OrderBundle\Dashboard
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrdersWidgetType extends AbstractWidgetType
{
    /**
     * @var OrderRepository
     */
    private $repository;


    /**
     * Constructor.
     *
     * @param OrderRepository $repository
     */
    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function render(WidgetInterface $widget, \Twig_Environment $twig)
    {
        $orders = $this->repository->findLatestRequiringTreatment();

        return $twig->render('EkynaOrderBundle:Admin/Dashboard:orders.html.twig', array(
            'orders' => $orders,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orders';
    }
}
