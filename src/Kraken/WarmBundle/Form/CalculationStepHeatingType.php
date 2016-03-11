<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Kraken\WarmBundle\Service\HotWaterService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'help_block' => 'Pomiń to pole jeśli nie znasz mocy urządzenia',
            ))
            ->add('fuel_consumptions', 'collection', [
                'label' => 'Dotychczasowe zużycie paliw na ogrzewanie',
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
                'label' => 'Dolicz grzanie wody kranowej (CWU)',
            ])
            ->add('hot_water_persons', null, [
                'label' => 'Ile osób będzie używać ciepłej wody?',
            ])
            ->add('hot_water_use', 'choice', [
                'label' => 'Intensywność zużycia wody',
                'choices' => HotWaterService::$usages,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
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
