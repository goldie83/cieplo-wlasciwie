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
                'help_block' => 'Nie bój się przyznać, jeśli dopiero zaczynasz. Tym bardziej twoja opinia będzie cenna, bo masz świeże spojrzenie na temat.',
                'expanded' => true,
            ])
            ->add('boilerPractice', 'choice', [
                'label' => 'Jak długo używasz tego kotła?',
                'choices' => [
                    'Nie więcej jak jeden sezon',
                    '1-2 lata',
                    '3-5 lat',
                    '5-10 lat',
                    'Ponad 10 lat',
                ],
                'expanded' => true,
            ])
            ->add('qualityRating', 'choice', [
                'label' => 'Jak oceniasz jakość wykonania kotła?',
                'choices' => [
                    5 => '5 - wszystko w porządku',
                    4 => '4 - nie wszystkie kanty doszlifowane, ale kocioł to nie lodówka',
                    3 => '3 - czasem coś się odkręci, urwie, rozszczelni, ale daje się naprawić',
                    2 => '2 - są elementy, które regularnie się psują',
                    1 => '1 - cud techniki: kocioł który ulega biodegradacji',
                ],
                'expanded' => true,
            ])
            ->add('qualityComment', 'textarea', [
                'label' => 'Komentarz odnośnie jakości wykonania',
                'help_block' => 'Pisz tutaj jeśli masz coś do dodania',
                'attr' => ['rows' => 8],
                'required' => false,
            ])
            ->add('warrantyRating', 'choice', [
                'label' => 'Jak oceniasz serwis gwarancyjny?',
                'choices' => [
                    0 => 'Nie było okazji skorzystać',
                    5 => '5 - nie mam zastrzeżeń',
                    4 => '4 - długo trzeba było czekać, ale swoje zrobili',
                    3 => '3 - nie wszystko mi się podobało, ale swoje zrobili',
                    2 => '2 - naprawili tak, że trzeba było poprawiać',
                    1 => '1 - w ogóle nie chcieli ze mną gadać',
                ],
                'expanded' => true,
            ])
            ->add('warrantyComment', 'textarea', [
                'label' => 'Komentarz odnośnie gwarancji i serwisu',
                'help_block' => 'Pisz tutaj jeśli masz coś do dodania',
                'attr' => ['rows' => 8],
                'required' => false,
            ])
            ->add('operationRating', 'choice', [
                'label' => 'Jak oceniasz łatwość codziennej obsługi?',
                'choices' => [
                    5 => '5 - nie mam z niczym problemu',
                    4 => '4 - na początku były długie wieczory w kotłowni, ale teraz ogarniam',
                    3 => '3 - czasem trzeba się z czymś dłużej pomęczyć',
                    2 => '2 - mało kiedy pali się bez problemów',
                    1 => '1 - obsługa kotłowni to koszmar',
                ],
                'data' => 0,
                'expanded' => true,
            ])
            ->add('operationComment', 'textarea', [
                'label' => 'Komentarz odnośnie obsługi kotła',
                'help_block' => 'Pisz tutaj jeśli masz coś do dodania',
                'attr' => ['rows' => 8],
                'required' => false,
            ])
            ->add('boilerPower', 'entity', [
                'class' => 'KrakenRankingBundle:BoilerPower',
                'label' => 'Której mocy kocioł masz u siebie?',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('bp')
                        ->where('bp.boiler = :boiler')
                        ->setParameter('boiler', $options['boiler_id'])
                    ;
                },
            ])
            ->add('houseArea', 'number', [
                'label' => 'Jaki mniej więcej metraż ogrzewasz?',
                'widget_addon_append' => [
                    'text' => 'mkw.',
                ],
            ])
            ->add('houseStandard', 'choice', [
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
                'expanded' => true,
            ])
            ->add('comment', 'textarea', [
                'label' => 'Ogólny komentarz',
                'help_block' => 'Pisz tutaj jeśli masz coś jeszcze do dodania, co nie zmieściło się lub nie pasowało gdzie indziej. Ten tekst nie będzie opublikowany.',
                'attr' => ['rows' => 8],
                'required' => false,
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
