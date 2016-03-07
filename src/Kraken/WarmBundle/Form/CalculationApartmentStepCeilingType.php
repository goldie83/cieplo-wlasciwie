<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CalculationApartmentStepCeilingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('whats_over', 'choice', array(
                'choices' => array(
                    'heated_room' => 'Ogrzewany lokal',
                    'unheated_room' => 'Nieogrzewany lokal',
                    'outdoor' => 'Świat zewnętrzny',
                ),
                'label' => 'Co znajduje się nad twoim mieszkaniem?',
            ))
            ->add('has_top_isolation', 'checkbox', array(
                'label' => 'Strop jest docieplony',
                'mapped' => false,
                'required' => false,
            ))
            ->add('top_isolation_layer', new LayerType(), array(
                'label' => 'Izolacja stropu mieszkania',
                'material_type' => 'for_ceiling',
                'mapped' => false,
                'required' => false,
            ))
            ->add('whats_under', 'choice', array(
                'choices' => array(
                    'heated_room' => 'Ogrzewany lokal',
                    'unheated_room' => 'Nieogrzewany lokal lub piwnica',
                    'outdoor' => 'Świat zewnętrzny',
                    'ground' => 'Grunt',
                ),
                'label' => 'Co znajduje się pod twoim mieszkaniem?',
            ))
            ->add('has_bottom_isolation', 'checkbox', array(
                'label' => 'Podłoga jest docieplona',
                'mapped' => false,
                'required' => false,
            ))
            ->add('bottom_isolation_layer', new LayerType(), array(
                'label' => 'Izolacja podłogi mieszkania',
                'material_type' => ['for_floor', 'for_ceiling'],
                'mapped' => false,
                'required' => false,
            ))
            ->add('number_external_walls', 'choice', array(
                'label' => 'Z ilu stron mieszkanie ma ściany zewnętrzne?',
                'choices' => [
                    'W ogóle nie ma',
                    'Z jednej',
                    'Z dwóch',
                    'Z trzech',
                    'Ze wszystkich stron',
                ],
                'help_block' => 'Chodzi o te ściany mieszkania, za którymi jest dwór/pole, a nie inne pomieszczenia',
            ))
            ->add('number_unheated_walls', 'choice', array(
                'label' => 'Z ilu stron mieszkanie sąsiaduje z nieogrzewanymi pomieszczeniami?',
                'choices' => [
                    'Wcale',
                    'Z jednej',
                    'Z dwóch',
                    'Z trzech',
                    'Ze wszystkich stron',
                ],
            ))
        ;
    }

    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\Apartment',
        ));
    }

    public function getName()
    {
        return 'calculation';
    }
}
