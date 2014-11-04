<?php

namespace Ekyna\Bundle\OrderBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ekyna\Bundle\CoreBundle\Form\DataTransformer\ObjectToIdentifierTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AddProductType
 * @package Ekyna\Bundle\OrderBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AddProductType extends AbstractType
{
    /**
     * @var array
     */
    protected $optionsConfiguration;

    /**
     * Constructor.
     *
     * @param array $optionsConfiguration
     */
    public function __construct(array $optionsConfiguration)
    {
        $this->optionsConfiguration = $optionsConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', 'ekyna_core_hidden_entity', array(
                'class' => 'EkynaProductBundle:AbstractProduct',
            ))
            ->add('quantity', 'integer', array('attr' => array('min' => 1)))
            ->add('submit', 'submit', array(
        	    'label' => 'Ajouter au panier'
            ))
            ->setAction($options['action'])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event){
            $data = $event->getData();
            $form = $event->getForm();
            $product = $data['product'];

            if ($product->hasOptions()) {
                $groups = $product->getOptionsGroups();
                foreach ($groups as $group) {
                    if(! array_key_exists($group, $this->optionsConfiguration)) {
                        throw new \RuntimeException(sprintf('Undefined option configuration "%s".', $group));
                    }
                    //$config = $this->optionsConfiguration[$group];
                    $form->add('option-'.$group, 'entity', array(
                        'label' => $this->optionsConfiguration[$group]['label'],
                        'required' => false,
                        'empty_value' => 'Choisissez une option',
                        'attr' => array(
                    	    'placeholder' => 'Choisissez une option',
                        ),
                        'class' => $this->optionsConfiguration[$group]['class'],
                        'query_builder' => function(EntityRepository $er) use ($product, $group) {
                            $qb = $er->createQueryBuilder('o');
                            return $qb
                                ->andWhere($qb->expr()->eq('o.product', ':product'))
                                ->andWhere($qb->expr()->eq('o.group', ':group'))
                                ->setParameter('product', $product)
                                ->setParameter('group', $group)
                            ;
                        },
                    )); 
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'em'     => null,
                'action' => ''
            ))
            ->setRequired(array('em', 'action'))
            ->setAllowedTypes(array(
            	'em'     => 'Doctrine\ORM\EntityManager',
                'action' => 'string'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
    	return 'ekyna_order_add_product';
    }
}
