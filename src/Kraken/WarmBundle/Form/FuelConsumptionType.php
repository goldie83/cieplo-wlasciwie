<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FuelConsumptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('fuel', null/*, array(
                'required' => $options['force_required'],
                'label' => 'Materiał',
                'query_builder' => function(EntityRepository $er ) use ($options) {
                    return $er->createQueryBuilder('m')
                        ->andWhere(sprintf('m.%s = 1', $options['material_type']))
                        ->orderBy('m.name', 'ASC');
                }
            )*/)
            ->add('consumption')
            ->add('cost')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\Layer',
            'material_type' => 'for_wall_construction_layer',
            'force_required' => false,
        ));
    }

    public function getName()
    {
        return 'layer';
    }
}
