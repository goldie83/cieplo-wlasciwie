<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalculationStepWallsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('construction_type', 'choice', array(
                'choices' => array(
                    'traditional' => 'Tradycyjna - murowana lub z drewna',
                    'canadian' => 'Szkieletowa - dom kanadyjski',
                ),
                'expanded' => true,
                'label' => 'Rodzaj konstrukcji budynku',
                'required' => true,
            ))
            ->add('wall_size', null, array(
                'required' => true,
                'widget_addon_append' => [
                    'text' => 'cm',
                ],
                'label' => 'Grubość ścian zewnętrznych',
                'help_block' => 'Zmierz całkowitą grubość ściany: od tynku wewnątrz do tynku na zewnątrz',
            ))
            ->add('primary_wall_material', null, array(
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->andWhere(sprintf('m.%s = 1', 'for_wall_construction_layer'))
                        ->orderBy('m.name', 'ASC');
                },
                'label' => 'Podstawowy materiał',
                'required' => false,
            ))
            ->add('secondary_wall_material', null, array(
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->andWhere(sprintf('m.%s = 1', 'for_wall_construction_layer'))
                        ->orderBy('m.name', 'ASC');
                },
                'label' => 'Dodatkowy materiał',
                'required' => false,
            ))
            ->add('has_isolation_inside', 'checkbox', array(
                'label' => 'Ściana ma izolację w środku',
                'required' => false,
                'mapped' => false,
            ))
            ->add('has_isolation_outside', 'checkbox', array(
                'label' => 'Dom jest docieplony',
                'required' => false,
                'mapped' => false,
            ))
            ->add('internal_isolation_layer', new LayerType(), array(
                'material_type' => 'for_wall_internal_layer',
                'material_label' => 'Izolacja wewnątrz ściany',
                'required' => false,
            ))
            ->add('external_isolation_layer', new LayerType(), array(
                'material_type' => 'for_wall_isolation_layer',
                'material_label' => 'Docieplenie od zewnątrz',
                'required' => false,
            ))
            ->add('number_doors', null, array(
                'label' => 'Liczba drzwi zewnętrznych',
                'help_block' => 'W części ogrzewanej',
                'widget_addon_append' => [
                    'text' => 'szt.',
                ],
            ))
            ->add('doors_type', 'choice', array(
                'required' => false,
                'choices' => array(
                    'old_wooden' => 'Stare drewniane',
                    'old_metal' => 'Stare metalowe',
                    'new_wooden' => 'Nowe drewniane',
                    'new_metal' => 'Nowe metalowe',
                ),
                'label' => 'Rodzaj drzwi zewnętrznych',
                'help_block' => 'Jeśli masz starsze i nowsze, wybierz te, których jest najwięcej',
            ))
            ->add('number_windows', null, array(
                'label' => 'Liczba okien',
                'help_block' => 'W części ogrzewanej',
                'widget_addon_append' => [
                    'text' => 'szt.',
                ],
            ))
            ->add('number_balcony_doors', 'number', array(
                'label' => 'Liczba drzwi balkonowych',
                'required' => false,
                'widget_addon_append' => [
                    'text' => 'szt.',
                ],
            ))
            ->add('number_huge_glazings', 'number', array(
                'label' => 'Liczba dużych przeszkleń',
                'required' => false,
                'widget_addon_append' => [
                    'text' => 'szt.',
                ],
            ))
            ->add('windows_type', 'choice', array(
                'choices' => [
                    'old_single_glass' => 'Stare z pojedynczą szybą',
                    'old_double_glass' => 'Stare z min. dwiema szybami',
                    'semi_new_double_glass' => 'Kilkunastoletnie z szybami zespolonymi',
                    'new_double_glass' => 'Współczesne dwuszybowe',
                    'new_triple_glass' => 'Współczesne trójszybowe',
                ],
                'label' => 'Rodzaj okien',
            ))
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
