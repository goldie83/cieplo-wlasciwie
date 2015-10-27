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
                'label' => 'Nazwa modelu lub producenta',
                'mapped' => false,
            ])
            ->add('power', 'number', [
                'label' => 'Moc',
                'mapped' => false,
            ])
            ->add('type', 'number', [
                'label' => 'Moc',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
//             'data_class' => 'AppBundle\Entity\Boiler',
        ]);
    }

    public function getName()
    {
        return 'search';
    }
}
