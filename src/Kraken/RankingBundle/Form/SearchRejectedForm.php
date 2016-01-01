<?php

namespace Kraken\RankingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchRejectedForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modelName', null, [
                'label' => 'Nazwa producenta lub modelu',
                'required' => false,
                'horizontal_label_class' => '',
            ])
            ->add('manufacturer', null, [
                'label' => 'Producent',
                'placeholder' => 'dowolny',
                'required' => false,
            ])
            ->add('category', null, [
                'property' => 'indentedName',
                'label' => 'Rodzaj',
                'placeholder' => 'dowolny',
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.sort');
                },
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
        return 'search_rejected';
    }
}
