<?php

namespace Ekyna\Bundle\OrderBundle\Form\EventListener;

use Ekyna\Bundle\OrderBundle\Helper\ItemHelperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class OrderItemTypeSubscriber
 * @package Ekyna\Bundle\OrderBundle\Form\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderItemTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @var ItemHelperInterface
     */
    private $itemHelper;

    /**
     * @var array
     */
    private $fields;


    /**
     * Constructor.
     *
     * @param ItemHelperInterface $itemHelper
     * @param array               $fields
     */
    public function __construct(ItemHelperInterface $itemHelper, array $fields)
    {
        $this->itemHelper = $itemHelper;
        $this->fields     = $fields;
    }

    /**
     * Form pre set data event handler.
     *
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $item = $event->getData();

        foreach ($this->fields as $field) {
            list ($name, $type, $options) = $field;
            if (null !== $item) {
                $options = array_replace($options, $this->itemHelper->getFormOptions($item, $name));
            }
            $form->add($name, $type, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => ['onPreSetData', 0],
        ];
    }
}
