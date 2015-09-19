<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrderPaymentType
 * @package Ekyna\Bundle\OrderBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderPaymentType extends AbstractType
{
    /**
     * @var string
     */
    protected $dataClass;


    /**
     * Constructor.
     *
     * @param $dataClass
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['admin_mode']) {
            $builder->add('notes', 'text', array(
                'label'    => 'ekyna_order.payment.field.notes',
                'required' => false,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'class' => $this->dataClass,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ekyna_payment_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_order_order_payment';
    }
}
