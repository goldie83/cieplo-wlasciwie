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
                'horizontal_label_class' => $options['vertical'] ? ' ' : 'col-sm-4',
            ])
            ->add('manufacturer', null, [
                'label' => 'Producent',
                'placeholder' => 'dowolny',
                'required' => false,
                'horizontal_label_class' => $options['vertical'] ? ' ' : 'col-sm-4',
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
                'horizontal_label_class' => $options['vertical'] ? ' ' : 'col-sm-4',
            ])
            ->add('fuelType', null, [
                'label' => 'Paliwa',
                'widget_type' => 'inline',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'horizontal_label_class' => $options['vertical'] ? ' ' : 'col-sm-4',
            ])
            ->add('power', 'choice', [
                'placeholder' => 'dowolna',
                'choices' => ['10' => 'do 10kW', '15' => '10‒15kW', '20' => '15‒20kW', '25' => '20‒25kW', '25+' => 'ponad 25kW'],
                'label' => 'Moc',
                'required' => false,
                'horizontal_label_class' => $options['vertical'] ? ' ' : 'col-sm-4',
            ])
            ->add('material', 'choice', [
                'placeholder' => 'dowolny',
                'choices' => ['steel' => 'stal', 'cast_iron' => 'żeliwo'],
                'label' => 'Materiał',
                'required' => false,
                'horizontal_label_class' => $options['vertical'] ? ' ' : 'col-sm-4',
            ])
            ->add('normClass', 'choice', [
                'choices' => [3 => 3, 4 => 4, 5 => 5],
                'label' => 'Klasa',
                'help_block' => 'wg normy PN-EN 303-5:2012',
                'placeholder' => 'dowolna',
                'required' => false,
                'horizontal_label_class' => $options['vertical'] ? ' ' : 'col-sm-4',
            ])
            ->add('rating', 'choice', [
                'choices' => ['A', 'B', 'C', 'D', 'E'],
                'label' => 'Ocena w rankingu',
                'placeholder' => 'dowolna',
                'required' => false,
                'horizontal_label_class' => $options['vertical'] ? ' ' : 'col-sm-4',
            ])
            ->add('forClosedSystem', null, [
                'label' => 'Dopuszczenie do układu zamkniętego',
                'required' => false,
                'horizontal_label_class' => $options['vertical'] ? ' ' : 'col-sm-4',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kraken\RankingBundle\Entity\Search',
            'vertical' => false
        ]);
    }

    public function getName()
    {
        return 'search';
    }
}
