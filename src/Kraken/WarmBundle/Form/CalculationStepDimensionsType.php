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
                'data' => 'no',
                'mapped' => false,
                'label' => 'Powierzchnia zabudowy',
            ])
            ->add('area', 'number', [
                'widget_addon_append' => [
                    'text' => 'mkw.',
                ],
                'required' => false,
                'label' => 'Powierzchnia zabudowy',
            ])
            ->add('building_shape', 'choice', [
                'choices' => [
                    'regular' => 'Mniej więcej regularny czworokąt',
                    'irregular' => 'Nieregularny kształt',
                ],
                'data' => 'regular',
                'required' => true,
                'label' => 'Kształt obrysu budynku',
            ])
            ->add('building_length', 'number', [
                'widget_addon_append' => [
                    'text' => 'm',
                ],
                'required' => false,
                'label' => 'Długość obrysu budynku',
            ])
            ->add('building_width', 'number', [
                'widget_addon_append' => [
                    'text' => 'm',
                ],
                'required' => false,
                'label' => 'Szerokość obrysu budynku',
            ])
            ->add('building_contour_free_area', 'number', [
                'widget_addon_append' => [
                    'text' => 'mkw.',
                ],
                'required' => false,
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
                'required' => true,
                'label' => 'Dom jest',
            ])
            ->add('building_roof', 'choice', [
                'choices' => [
                    'steep' => 'Skośny',
                    'flat' => 'Płaski',
                ],
                'required' => true,
                'label' => 'Dach jest',
            ])
            ->add('has_basement', 'checkbox', [
                'required' => false,
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
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'label' => 'Które piętra są ogrzewane?',
            ])
            ->add('floor_height', 'choice', array(
                'choices' => array(
                    2.3 => 'Niskie (poniżej 2,5m)',
                    2.6 => 'Standardowe (ok. 2,6m)',
                    3.0 => 'Wysokie (3m i więcej)',
                ),
                'label' => 'Wysokość pięter',
                'required' => true,
            ))
            ->add('has_balcony', null, [
                'label' => 'Dom ma balkon(y)',
            ])
            ->add('has_garage', null, [
                'label' => 'Dom ma garaż w bryle budynku',
            ])
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
