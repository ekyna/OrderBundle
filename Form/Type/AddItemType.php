<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AddItemType
 * @package Ekyna\Bundle\OrderBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AddItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'integer', array('attr' => array('min' => 1)))
            ->add('submit', 'submit', array(
                'label' => 'Ajouter au panier'
            ))
            ->setAction($options['action'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Ekyna\Bundle\OrderBundle\Model\AddOrderItem',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_order_add_item';
    }
}
