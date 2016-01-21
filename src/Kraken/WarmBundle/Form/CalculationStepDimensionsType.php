<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CalculationStepDimensionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('has_area', 'choice', [
                'choices' => [
                    'yes' => 'Mam wyliczoną',
                    'no' => 'Znam wymiary obrysu budynku',
                ],
                'expanded' => true,
                'required' => true,
                'mapped' => false,
                'label' => 'Jak zacząć?',
            ])
            ->add('area', 'number', [
                'widget_addon_append' => [
                    'text' => 'mkw.',
                ],
                'required' => false,
                'mapped' => false,
                'label' => 'Powierzchnia zabudowy',
            ])
            ->add('building_shape', 'choice', [
                'choices' => [
                    'regular' => 'Regularny prostokąt',
                    'irregular' => 'Nieregularny kształt',
                ],
                'data' => 'regular',
                'required' => true,
                'mapped' => false,
                'label' => 'Kształt obrysu budynku',
            ])
            ->add('building_length', 'number', [
                'widget_addon_append' => [
                    'text' => 'm',
                ],
                'required' => false,
                'mapped' => false,
                'label' => 'Długość',
            ])
            ->add('building_width', 'number', [
                'widget_addon_append' => [
                    'text' => 'm',
                ],
                'required' => false,
                'mapped' => false,
                'label' => 'Szerokość',
            ])
            ->add('building_contour_free_area', 'number', [
                'widget_addon_append' => [
                    'text' => 'mkw.',
                ],
                'required' => false,
                'mapped' => false,
                'label' => 'Powierzchnia wcięć w obrysie budynku',
            ])
            ->add('building_floors', 'choice', [
                'choices' => [
                    1 => 'Parterowy',
                    2 => 'Jednopiętrowy',
                    3 => 'Dwupiętrowy',
                    4 => 'Trzypiętrowy',
                    5 => 'Czteropiętrowy',
                ],
                'data' => 1,
                'required' => true,
                'mapped' => false,
                'label' => 'Dom jest',
            ])
            ->add('building_roof', 'choice', [
                'choices' => [
                    'steep' => 'Skośny',
                    'flat' => 'Płaski',
                ],
                'data' => 'steep',
                'required' => true,
                'mapped' => false,
                'label' => 'Dach jest',
            ])
            ->add('building_has_basement', 'checkbox', [
                'required' => false,
                'mapped' => false,
                'label' => 'Dom jest podpiwniczony',
            ])
            ->add('building_heated_floors', 'choice', [
                'choices' => [
                    0 => 'Piwnica',
                    1 => 'Parter',
                    2 => '1. piętro',
                    3 => '2. piętro',
                    4 => '3. piętro',
                    5 => '4. piętro',
                    6 => '5. piętro',
                ],
                'data' => [1, 2],
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'allow_extra_fields' => true,
                'mapped' => false,
                'label' => 'Które piętra są ogrzewane?',
            ])
            ->add('floor_height', 'choice', array(
                'choices' => array(
                    '2.3' => 'Niskie (poniżej 2,5m)',
                    '2.6' => 'Standardowe (ok. 2,6m)',
                    '3.0' => 'Wysokie (3m i więcej)',
                ),
                'label' => 'Wysokość pięter',
                'mapped' => false,
                'data' => '2.6',
                'required' => true,
            ))
        ;
    }

    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\Calculation',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'calculation';
    }
}
