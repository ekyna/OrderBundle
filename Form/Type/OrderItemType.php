<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Ekyna\Bundle\OrderBundle\Form\EventListener\OrderItemTypeSubscriber;
use Ekyna\Bundle\OrderBundle\Helper\ItemHelperInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class OrderItemType
 * @package Ekyna\Bundle\OrderBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemType extends ResourceFormType
{
    /**
     * @var ItemHelperInterface
     */
    protected $itemHelper;


    /**
     * Constructor.
     *
     * @param string $class
     * @param ItemHelperInterface $itemHelper
     */
    public function __construct($class, ItemHelperInterface $itemHelper)
    {
        parent::__construct($class);

        $this->itemHelper = $itemHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new OrderItemTypeSubscriber($this->itemHelper, $this->getFields($options));
        $builder->addEventSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_order_order_item';
    }

    /**
     * Returns the fields definitions.
     *
     * @param array $options
     * @return array
     */
    protected function getFields(array $options)
    {
        return [
            ['designation', 'text', [
                'label' => 'ekyna_core.field.designation',
                'sizing' => 'sm',
                'attr' => [
                    'label_col' => 4,
                    'widget_col' => 8
                ]
            ]],
            ['reference', 'text', [
                'label' => 'ekyna_core.field.reference',
                'sizing' => 'sm',
                'attr' => [
                    'label_col' => 4,
                    'widget_col' => 8
                ]
            ]],
            ['weight', 'integer', [
                'label' => 'ekyna_core.field.weight',
                'sizing' => 'sm',
                'attr' => [
                    'input_group' => ['append' => 'g'],
                    'min' => 0,
                    'label_col' => 4,
                    'widget_col' => 8
                ],
            ]],
            ['price', 'number', [
                'label' => 'ekyna_core.field.price',
                'precision' => 5,
                'sizing' => 'sm',
                'attr' => [
                    'input_group' => ['append' => '€'],
                    'label_col' => 4,
                    'widget_col' => 8
                ],
            ]],
            ['quantity', 'integer', [
                'label' => 'ekyna_core.field.quantity',
                'sizing' => 'sm',
                'attr' => [
                    'min' => 1,
                    'label_col' => 4,
                    'widget_col' => 8
                ],
            ]],
            ['tax', 'ekyna_resource', [
                'label' => 'ekyna_core.field.tax',
                'class' => 'Ekyna\Bundle\OrderBundle\Entity\Tax',
                'multiple' => false,
                'required' => false,
                'property' => 'name',
                'empty_value' => 'ekyna_core.field.tax',
                'allow_new' => $options['admin_mode'],
                'sizing' => 'sm',
                'attr' => [
                    'placeholder' => 'ekyna_core.field.tax',
                    'label_col' => 4,
                    'widget_col' => 8
                ],
            ]],
            ['position', 'hidden', [
                'attr' => [
                    'data-collection-role' => 'position'
                ]
            ]]
        ];
    }
}
