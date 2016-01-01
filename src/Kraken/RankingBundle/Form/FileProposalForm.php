<?php

namespace Kraken\RankingBundle\Form;

use Doctrine\ORM\EntityRepository;
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
            ])
            ->add('content', null, [
                'label' => 'Tu pisz pytania, wyjaśnienia, szczególne życzenia odnośnie propozycji',
                'attr' => ['rows' => '10'],
                'required' => false,
            ])
            ->add('email', null, [
                'label' => 'Twój adres e-mail (opcjonalnie)',
                'attr' => array(
                    'help_text' => 'Jeśli chcesz otrzymać informację gdy tylko kocioł zostanie dodany do rankingu, zostaw tutaj swój e-mail. Adres będzie użyty wyłącznie do tego celu i nie zostanie nigdzie udostępniony.',
                ),
                'required' => false,
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
