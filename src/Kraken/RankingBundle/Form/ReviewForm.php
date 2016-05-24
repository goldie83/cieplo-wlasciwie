<?php

namespace Kraken\RankingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Kraken\RankingBundle\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reviewExperiences', 'collection', [
                'label' => 'Doświadczenia ludzi',
                'entry_type' => 'Kraken\RankingBundle\Form\ReviewExperienceForm',
            ])
            ->add('ownExperiences', 'collection', [
                'label' => ' ',
                'entry_type' => 'Kraken\RankingBundle\Form\ExperienceForm',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'widget_add_btn' => [
                    'label' => 'Dodaj doświadczenie',
                ],
                'horizontal_label_class' => ' ',
                'horizontal_input_wrapper_class' => 'col-sm-12',
                'show_legend' => false,
                'options' => [
                    'horizontal' => true,
                    'label_render' => false,
                ],
                'required' => false,
            ])
            ->add('practice', 'choice', [
                'label' => 'Twój ogólny staż w kotłowni',
                'choices' => [
                    'Nie więcej jak jeden sezon',
                    '1-2 lata',
                    '3-5 lat',
                    '5-10 lat',
                    'Ponad 10 lat',
                ],
                'help_block' => 'Nie wstydź się przyznać, jeśli dopiero zaczynasz. Tym bardziej twoja opinia będzie cenna, bo masz świeże spojrzenie na temat.',
                'mapped' => false,
                'expanded' => true,
            ])
            ->add('boiler_practice', 'choice', [
                'label' => 'Jak długo używasz tego kotła?',
                'choices' => [
                    'Nie więcej jak jeden sezon',
                    '1-2 lata',
                    '3-5 lat',
                    '5-10 lat',
                    'Ponad 10 lat',
                ],
                'mapped' => false,
                'expanded' => true,
            ])
            ->add('boiler_power', 'entity', [
                'class' => 'KrakenRankingBundle:BoilerPower',
                'label' => 'Której mocy kocioł masz u siebie?',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('bp')
                        ->where('bp.boiler = :boiler')
                        ->setParameter('boiler', $options['boiler_id'])
                    ;
                },
                'mapped' => false,
            ])
            ->add('fuels_practice', 'textarea', [
                'label' => 'Doświadczenia z paliwami',
                'attr' => ['rows' => 8],
                'mapped' => false,
            ])
            ->add('house_area', 'number', [
                'label' => 'Jaki metraż ogrzewasz?',
                'widget_addon_append' => [
                    'text' => 'mkw.',
                ],
                'mapped' => false,
            ])
            ->add('house_standard', 'choice', [
                'label' => 'Jak docieplony jest dom?',
                'label_attr' => ['style' => 'text-align:left'],
                'choices' => [
                    'Ekstra - min. 20cm ocieplenia na ścianach, trójszybowe okna',
                    'Bardzo dobrze - ok. 15cm na ścianach, nie mniej w podłodze i dachu',
                    'Dobrze - ok. 10cm na ścianach',
                    'Przeciętnie - ok. 5cm na ścianach',
                    'Prawie wcale - jedynie pustka powietrzna w murze, w dachu/podłodze żużel/trociny lub coś podobnego z epoki przedstyropianowej',
                    'Ani trochę - goły mur i beton, względnie drewno',
                ],
                'mapped' => false,
                'expanded' => true,
            ])
            ->add('rating', 'choice', [
                'label' => 'Jak ogólnie oceniasz użytkowanie tego kotła?',
                'label_attr' => ['style' => 'text-align:left'],
                'choices' => [
                    5 => '5 - tylko sypię opał i wynoszę popiół',
                    4 => '4 - są drobne niedogodności z którymi sobie radzę',
                    3 => '3 - momentami się użeram, ale wygrywam',
                    2 => '2 - jedyna zaleta: jako tako grzeje',
                    1 => '1 - dopłacę byle to ode mnie zabrali',
                ],
                'mapped' => false,
                'expanded' => true,
            ])
            ->add('comment', 'textarea', [
                'label' => 'Luźny komentarz',
                'help_block' => 'Pisz tutaj jeśli masz coś jeszcze do dodania, co nie zmieściło się lub nie pasowało gdzie indziej. Ten tekst nie będzie opublikowany.',
                'attr' => ['rows' => 8],
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kraken\RankingBundle\Entity\Review',
            'boiler_id' => null,
        ]);
    }

    public function getName()
    {
        return 'review';
    }
}
