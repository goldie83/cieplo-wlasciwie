<?php

namespace Kraken\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modelName', null, [
                'label' => 'Nazwa producenta lub modelu',
                'required' => false,
            ])
            ->add('category', null, [
                'label' => 'Rodzaj',
                'placeholder' => 'dowolny',
                'required' => false,
            ])
            ->add('fuelType', null, [
                'label' => 'Paliwa',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->add('power', 'choice', [
                'choices' => ['10' => 'do 10kW', '15' => '10‒15kW', '20' => '15‒20kW', '25' => '20‒25kW', '25+' => 'ponad 25kW'],
                'label' => 'Moc',
                'required' => false,
            ])
            ->add('normClass', 'choice', [
                'choices' => [3, 4, 5],
                'label' => 'Klasa',
                'placeholder' => 'dowolna',
                'required' => false,
            ])
            ->add('rating', 'choice', [
                'choices' => ['A', 'B', 'C', 'D', 'E'],
                'label' => 'Ocena w rankingu',
                'placeholder' => 'dowolna',
                'required' => false,
            ])
            ->add('forClosedSystem', null, [
                'label' => 'Kotły do układu zamkniętego',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kraken\RankingBundle\Entity\Search',
        ]);
    }

    public function getName()
    {
        return 'search';
    }
}
