<?php

namespace Kraken\RankingBundle\Form;

use Doctrine\ORM\EntityRepository;
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
                'horizontal_label_class' => '',
            ])
            ->add('manufacturer', null, [
                'label' => 'Producent',
                'placeholder' => 'dowolny',
                'required' => false,
            ])
            ->add('category', null, [
                'choice_label' => 'indentedName',
                'label' => 'Rodzaj',
                'placeholder' => 'dowolny',
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.sort');
                },
            ])
            ->add('fuelType', null, [
                'label' => 'Paliwa',
                'widget_type' => 'inline',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->add('power', 'choice', [
                'placeholder' => 'dowolna',
                'choices' => ['10' => 'do 10kW', '15' => '10‒15kW', '20' => '15‒20kW', '25' => '20‒25kW', '25+' => 'ponad 25kW'],
                'label' => 'Moc',
                'required' => false,
            ])
            ->add('material', 'choice', [
                'placeholder' => 'dowolny',
                'choices' => ['steel' => 'stal', 'cast_iron' => 'żeliwo'],
                'label' => 'Materiał',
                'required' => false,
            ])
            ->add('normClass', 'choice', [
                'choices' => [3, 4, 5],
                'label' => 'Klasa',
                'help_block'  => 'wg normy PN-EN 303-5:2012',
                'attr' => array(
                    'help_text' => 'wg normy PN-EN 303-5:2012',
                ),
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
                'label' => 'Dopuszczony do układu zamkniętego',
                'required' => false,
            ])
            ->add('needsFixing', null, [
                'label' => 'Nie wymaga poprawek aby palić czysto',
                'help_block' => 'Pokazuje tylko te kotły, w których nie trzeba niczego poprawiać po fabryce, aby palić bez dymu i nadmiernego grzania komina.',
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
