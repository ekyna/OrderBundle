<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ->add('number', 'text', array(
                'label' => 'ekyna_core.field.number',
                'required' => true,
                'disabled' => true,
            ))
            ->add('user', 'ekyna_user_search')
            ->add('items', 'ekyna_core_collection', array(
                'label'           => false,
                'type'            => 'ekyna_order_order_item',
                'allow_add'       => true,
                'allow_delete'    => true,
                'allow_sort'      => true,
                'add_button_text' => 'ekyna_core.button.add',
                'sub_widget_col'  => 11,
                'button_col'      => 1,
                'attr'            => array(
                    'widget_col' => 12
                ),
                'options'         => array(
                    'label'    => false,
                    'required' => false,
                ),
            ))
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
