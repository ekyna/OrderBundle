<?php

namespace Ekyna\Bundle\OrderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ekyna\Bundle\OrderBundle\Event\OrderEvent;
use Ekyna\Bundle\OrderBundle\Event\OrderEvents;
use Faker\Factory;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadOrderData
 * @package Ekyna\Bundle\OrderBundle\DataFixtures\ORM
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class LoadOrderData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var \libphonenumber\PhoneNumberUtil
     */
    private $phoneUtil;

    /**
     * @var \Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var array
     */
    private $genders;


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
        $this->faker = Factory::create($this->container->getParameter('hautelook_alice.locale'));
        $this->phoneUtil = PhoneNumberUtil::getInstance();

        $this->addressRepository = $this->container->get('ekyna_user.address.repository');
        $genderClass = $this->container->getParameter('ekyna_user.gender_class');
        $this->genders = call_user_func($genderClass.'::getConstants');


        $taxes = $this->container->get('ekyna_order.tax.repository')->findAll();
        //$users = $this->container->get('ekyna_user.user.repository')->findRandomBy(['enabled' => true], 10);
        $itemClass = $this->container->getParameter('ekyna_order.order_item.class');

        $repository = $this->container->get('ekyna_order.order.repository');
        $operator = $this->container->get('ekyna_order.order.operator');
        $dispatcher = $this->container->get('event_dispatcher');;

        for ($o = 0; $o < 10; $o++) {
            $order = $repository->createNew();

            for ($i=0; $i < rand(1,3); $i++) {
                /** @var \Ekyna\Component\Sale\Order\OrderItemInterface $item */
                $item = new $itemClass;
                $item
                    ->setReference($this->faker->bothify('####-????'))
                    ->setDesignation(trim($this->faker->sentence(rand(2,5), false), '.'))
                    ->setPrice($price = rand(5000, 100000)/100)
                    ->setTax(1 < rand(1,4) ? $this->faker->randomElement($taxes) : null)
                    ->setQuantity(rand(1,10))
                    ->setWeight(1 < rand(1,4) ? rand(0,12)*100+200 : 0)
                ;
                $order->addItem($item);
            }

            $order
                ->setEmail($this->faker->email)
                ->setGender($this->faker->randomElement($this->genders))
                ->setFirstName($this->faker->firstName)
                ->setLastName($this->faker->lastName)
                ->setInvoiceAddress($this->createAddress())
            ;

            if ($order->requiresShipment()) {
                $order->setSameAddress(true);
            } else {
                $sameAddress = 1 < rand(1,4);
                $order->setSameAddress($sameAddress);
                if (!$sameAddress) {
                    $order->setDeliveryAddress($this->createAddress());
                }
            }

            $event = $dispatcher->dispatch(OrderEvents::CONTENT_CHANGE, new OrderEvent($order));
            if ($event->isPropagationStopped()) {
                throw new \Exception('Failed to create order.');
            }
        }
    }

    /**
     * @return \Ekyna\Bundle\UserBundle\Model\AddressInterface
     */
    private function createAddress()
    {
        /** @var \Ekyna\Bundle\UserBundle\Model\AddressInterface $address */
        $address = $this->addressRepository->createNew();
        $address
            ->setCompany(30 < rand(0,100) ? $this->faker->sentence(3) : null)
            ->setGender($this->faker->randomElement($this->genders))
            ->setFirstName($this->faker->firstName)
            ->setLastName($this->faker->lastName)
            ->setStreet($this->faker->streetAddress)
            ->setSupplement(70 < rand(0,100) ? $this->faker->sentence(4) : null)
            ->setPostalCode(str_replace(' ', '' ,$this->faker->postcode))
            ->setCity($this->faker->city)
            ->setCountry($this->faker->countryCode)
            ->setPhone($this->phoneUtil->parse($this->faker->phoneNumber, 'FR'))
            ->setMobile(60 < rand(0,100) ? $this->phoneUtil->parse($this->faker->phoneNumber, 'FR') : null)
        ;
        return $address;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 99;
    }
}
