<?php

namespace Kraken\WarmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalculationStepCeilingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('has_top_isolation', 'choice', [
                'choices' => [
                    'yes' => 'Tak, jest jakaś izolacja',
                    'no' => 'Nie, nie ma żadnej izolacji',
                ],
                'expanded' => true,
                'required' => true,
                'mapped' => false,
                'label' => 'Czy jest jakakolwiek '.strtolower($options['top_isolation_label']).'?',
            ])
            ->add('top_isolation_layer', new LayerType(), array(
                'label' => $options['top_isolation_label'],
                'material_type' => 'for_ceiling',
                'required' => false,
            ))
            ->add('has_bottom_isolation', 'choice', [
                'choices' => [
                    'yes' => 'Tak, jest jakaś izolacja',
                    'no' => 'Nie, nie ma żadnej izolacji',
                ],
                'expanded' => true,
                'required' => true,
                'mapped' => false,
                'label' => 'Czy jest jakakolwiek '.strtolower($options['bottom_isolation_label']).'?',
            ])
            ->add('bottom_isolation_layer', new LayerType(), array(
                'label' => $options['bottom_isolation_label'],
                'material_type' => ['for_floor', 'for_ceiling'],
                'required' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\House',
            'top_isolation_label' => 'Izolacja dachu',
            'bottom_isolation_label' => 'Izolacja podłogi parteru',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'calculation';
    }
}
