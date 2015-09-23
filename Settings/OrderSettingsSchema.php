<?php

namespace Ekyna\Bundle\OrderBundle\Settings;

use Ekyna\Bundle\SettingBundle\Schema\AbstractSchema;
use Ekyna\Bundle\SettingBundle\Schema\SettingsBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class OrderSettingsSchema
 * @package Ekyna\Bundle\OrderBundle\Settings
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class OrderSettingsSchema extends AbstractSchema
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilder $builder)
    {
        $builder
            ->setDefaults(array_merge([
                'document_footer' => 'Pied de page des documents',
            ], $this->defaults))
            ->setAllowedTypes('document_footer', 'string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('document_footer', 'tinymce', [
                'label'       => 'ekyna_order.order.field.document_footer',
                'theme'       => 'simple',
                'constraints' => [
                    new NotBlank()
                ]
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'ekyna_order.order.label.plural';
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate()
    {
        return 'EkynaOrderBundle:Admin/Settings:show.html.twig';
    }

    /**
     * {@inheritDoc}
     */
    public function getFormTemplate()
    {
        return 'EkynaOrderBundle:Admin/Settings:form.html.twig';
    }

    public function getName()
    {
        return 'ekyna_order_settings';
    }
}
