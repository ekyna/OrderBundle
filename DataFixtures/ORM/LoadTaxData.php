<?php

namespace Ekyna\Bundle\OrderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadTaxData
 * @package Ekyna\Bundle\OrderBundle\DataFixtures\ORM
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class LoadTaxData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $om)
    {
        $repo = $this->container->get('ekyna_agenda.event.repository');

        $taxes = array(
            'Demo tax 20%' => 0.2,
            'Demo tax 10%' => 0.1,
            'Demo tax 5%' => 0.05,
        );

        foreach ($taxes as $name => $rate) {
            /** @var \Ekyna\Bundle\OrderBundle\Entity\Tax $tax */
            $tax = $repo->createNew();
            $tax
                ->setName($name)
                ->setRate($rate)
            ;
            $om->persist($tax);
        }

        $om->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
