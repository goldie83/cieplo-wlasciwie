<?php

namespace Kraken\RankingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileProposalForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', null, [
                'label' => 'Adres strony producenta / z informacjami o kotle',
                'required' => true,
                'horizontal_label_class' => '',
            ])
            ->add('content', null, [
                'label' => 'Tu pisz pytania, wyjaśnienia, szczególne życzenia odnośnie propozycji (jeśli jakieś masz)',
                'attr' => ['rows' => '10'],
                'required' => false,
                'horizontal_label_class' => '',
            ])
            ->add('email', null, [
                'label' => 'Twój adres e-mail (opcjonalnie)',
                'help_block' => 'Jeśli chcesz otrzymać informację gdy tylko kocioł zostanie dodany do rankingu, zostaw tutaj swój e-mail. Adres będzie użyty wyłącznie do tego celu i nie zostanie nigdzie udostępniony.',
                'required' => false,
                'horizontal_label_class' => '',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kraken\RankingBundle\Entity\Proposal',
        ]);
    }

    public function getName()
    {
        return 'proposal';
    }
}
