<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OrderType
 * @package Ekyna\Bundle\OrderBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderType extends ResourceFormType
{
    /**
     * @var string
     */
    protected $userClass;


    /**
     * Constructor.
     *
     * @param string $orderClass
     * @param string $userClass
     */
    public function __construct($orderClass, $userClass)
    {
        parent::__construct($orderClass);

        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', 'text', [
                'label' => 'ekyna_core.field.number',
                'disabled' => true,
            ])
            ->add('user', 'ekyna_user_search', [
                'required' => false,
            ])
            ->add('identity', 'ekyna_user_identity')
            ->add('email', 'email', [
                'label' => 'ekyna_core.field.email',
            ])
            ->add('invoiceAddress', 'ekyna_user_address', [
                'label' => 'ekyna_order.order.field.invoice_address',
            ])
            ->add('sameAddress', 'checkbox', [
                'label' => 'ekyna_order.order.field.same_address',
                'required' => false,
                'attr' => [
                    'align_with_widget' => true,
                ],
            ])
            ->add('deliveryAddress', 'ekyna_user_address', [
                'label' => 'ekyna_order.order.field.delivery_address',
                'required' => false,
            ])
            ->add('items', 'ekyna_order_order_items')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'validation_groups' => ['Default', 'Order'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_order_order';
    }
}
