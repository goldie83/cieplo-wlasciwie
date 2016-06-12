<?php

namespace Kraken\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'Krótki opis',
                'help_block' => 'Np. "Ruszt ruchomy często się blokuje" albo "Kocioł elegancko spala najtańszy miał"',
            ])
            ->add('content', null, [
                'label' => 'Szersze wyjaśnienie, jeśli konieczne',
                'required' => false,
                'attr' => ['rows' => 8],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kraken\RankingBundle\Entity\Experience',
        ]);
    }

    public function getName()
    {
        return 'experience';
    }
}
