<?php

namespace Kraken\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewExperienceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//             ->add('review')
//             ->add('experience')
            ->add('confirmed', 'choice', [
                'choices' => [0 => 'Nie potwierdzam', 1 => 'Potwierdzam'],
                'expanded' => true,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kraken\RankingBundle\Entity\ReviewExperience',
        ]);
    }

    public function getName()
    {
        return 'review_experience';
    }
}
