<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CalculationStepCeilingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('top_isolation_layer', new LayerType(), array(
                'label' => 'Izolacja od góry',
                'material_type' => 'for_ceiling',
                'required' => false,
            ))
            ->add('bottom_isolation_layer', new LayerType(), array(
                'label' => 'Izolacja od dołu',
                'material_type' => ['for_floor', 'for_ceiling'],
                'required' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\House',
        ));
    }

    public function getName()
    {
        return 'calculation';
    }
}
