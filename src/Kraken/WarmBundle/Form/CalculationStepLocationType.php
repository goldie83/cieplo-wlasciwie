<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CalculationStepLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('construction_year', 'choice', array(
                'choices' => [
                    2011 => '2011 – 2016',
                    2000 => '2000 – 2010',
                    1990 => 'lata 90-te',
                    1980 => 'lata 80-te',
                    1970 => 'lata 70-te',
                    1960 => 'lata 60-te',
                    1950 => 'lata 50-te',
                    1940 => 'lata 40-te',
                    1939 => 'gdzieś przed II wojną',
                    1914 => 'gdzieś przed I wojną',
                ],
                'required' => true,
                'label' => 'Lata budowy domu',
            ))
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\Calculation',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'calculation';
    }
}
