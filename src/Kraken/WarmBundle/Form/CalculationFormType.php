<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CalculationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('building_type', 'choice', array(
                'choices' => array(
                    'single_house' => 'Dom jednorodzinny wolnostojący',
                    'double_house' => 'Bliźniak',
                    'row_house' => 'Zabudowa szeregowa',
                    'apartment' => 'Mieszkanie',
                ),
                'label' => 'Rodzaj budynku',
            ))
            ->add('construction_year', 'choice', array(
                'choices' => array_combine(range(date("Y"), 1900), range(date("Y"), 1900)),
                'required' => true,
                'label' => 'Rok budowy domu',
                'attr'  => array(
                    'help_text' => 'Dokładność +/- 10 lat jest wystarczająca',
                ),
            ))
            ->add('heating_device', null, [
                'label' => 'Urządzenie grzewcze',
                'query_builder' => function(EntityRepository $er ) use ($options) {
                    return $er->createQueryBuilder('hd')
                        ->andWhere('hd.for_legacy_setup = 1')
                        ->orderBy('hd.name', 'ASC');
                }
            ])
            ->add('stove_power', 'number', array(
                'label' => 'Moc urządzenia grzewczego',
                'required' => false,
                'widget_addon_append' => [
                    'text'  => 'kW'
                ]
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
                    'horizontal_input_wrapper_class' => "col-lg-8",
                ]
            ])
            ->add('email', null, array(
                'label' => 'Twój adres e-mail',
                'required' => false,
            ))
            ->add('indoor_temperature', 'number', array(
                'label' => 'Temperatura w mieszkaniu zimą',
                'required' => true,
                'widget_addon_append' => [
                    'text'  => '&deg;C',
                ],
                'help_block' => 'Podaj średnią dobową temperaturę, jaką uznajesz za komfortową w domu zimą bez noszenia swetra i kalesonów. Np. w dzień +22, w nocy +18 - wpisz 20 stopni',
            ))
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
