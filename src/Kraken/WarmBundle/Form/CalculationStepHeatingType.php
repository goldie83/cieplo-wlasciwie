<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CalculationStepHeatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('heating_device', null, [
                'label' => 'Podstawowe źródło ciepła',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('hd')
                        ->andWhere('hd.for_legacy_setup = 1')
                        ->orderBy('hd.name', 'ASC');
                },
            ])
            ->add('stove_power', 'number', array(
                'label' => 'Moc grzewcza',
                'required' => false,
                'widget_addon_append' => [
                    'text' => 'kW',
                ],
            ))
            ->add('fuel_consumptions', 'collection', [
                'label' => 'Zużycie paliw',
                'required' => false,
                'type' => new FuelConsumptionType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'widget_add_btn' => [
                    'label' => 'Dodaj paliwo',
                ],
                'show_legend' => true,
                'options' => [
                    'label_render' => false,
                    'widget_addon_prepend' => [
                        'text' => '@',
                    ],
                    'horizontal_input_wrapper_class' => 'col-sm-9',
                ],
            ])
            ->add('email', null, array(
                'label' => 'Twój adres e-mail',
                'required' => false,
            ))
            ->add('indoor_temperature', 'number', array(
                'label' => 'Temperatura w mieszkaniu zimą',
                'required' => true,
                'widget_addon_append' => [
                    'text' => '&deg;C',
                ],
                'help_block' => 'Podaj temperaturę jaką uznajesz za komfortową bez noszenia dwóch swetrów i kalesonów. Za standardową temperaturę pokojową przyjmuje się 20 st.C',
            ))
            ->add('ventilation_type', 'choice', array(
                'choices' => array(
                    'natural' => 'Naturalna lub grawitacyjna',
                    'mechanical' => 'Mechaniczna',
                    'mechanical_recovery' => 'Mechaniczna z odzyskiem ciepła',
                ),
                'label' => 'Rodzaj wentylacji',
                'mapped' => false
            ))
            ->add('include_hot_water', null, [
                'label' => 'Dolicz ogrzewanie wody (CWU)',
            ])
            ->add('hot_water_persons', null, [
                'label' => 'Liczba osób używający ciepłej wody',
            ])
            ->add('hot_water_use', 'choice', [
                'label' => 'Intensywność zużycia wody',
                'choices' => [
                    'shower' => 'w domu tylko prysznice',
                    'shower_bath' => 'głównie prysznice, czasem wanna',
                    'bath' => 'codziennie wanna dla każdego',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\Calculation',
        ));
    }

    public function getName()
    {
        return 'calculation';
    }
}
