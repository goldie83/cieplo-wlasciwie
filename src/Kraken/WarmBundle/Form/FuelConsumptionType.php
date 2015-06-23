<?php

namespace Kraken\WarmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FuelConsumptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('fuel', null, [
                'label' => 'Rodzaj paliwa',
                'required' => false,
            ])
            ->add('consumption', null, [
                'label' => 'Zużycie w sezonie grzewczym',
                'required' => false,
                'widget_addon_append' => [
                    'text' => 't',
                ],
            ])
            ->add('cost', null, [
                'label' => 'Koszt',
                'required' => false,
                'widget_addon_append' => [
                    'text' => 'zł',
                ],
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kraken\WarmBundle\Entity\FuelConsumption',
        ]);
    }

    public function getName()
    {
        return 'fuel_consumption';
    }
}
