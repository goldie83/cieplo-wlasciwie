<?php

namespace Kraken\WarmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalculationApartmentStepDimensionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('area', 'number', [
                'widget_addon_append' => [
                    'text' => 'mkw.',
                ],
                'required' => false,
                'label' => 'Wymiary mieszkania',
            ])
            ->add('floor_height', 'choice', array(
                'choices' => array(
                    2.3 => 'Niskie (poniżej 2,5m)',
                    2.6 => 'Standardowe (ok. 2,6m)',
                    3.0 => 'Wysokie (3m i więcej)',
                ),
                'label' => 'Wysokość piętra',
                'required' => true,
            ))
            ->add('building_floors', 'choice', [
                'choices' => [
                    1 => 'Jednopoziomowe',
                    2 => 'Dwupoziomowe',
                    3 => 'Trzypoziomowe',
                ],
                'required' => true,
                'label' => 'Mieszkanie jest',
            ])
            ->add('building_heated_floors', 'choice', [
                'choices' => [
                    0 => 'I poziom',
                    1 => 'II poziom',
                    2 => 'III poziom',
                ],
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'label' => 'Które poziomy mieszkania są ogrzewane?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
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
