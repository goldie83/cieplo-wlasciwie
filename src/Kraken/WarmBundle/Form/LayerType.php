<?php

namespace Kraken\WarmBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('material', null, array(
                'required' => $options['force_required'],
                'label' => $options['material_label'],
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('m');
                    $types = is_array($options['material_type']) ? $options['material_type'] : [$options['material_type']];
                    foreach ($types as $type) {
                        $qb->orWhere(sprintf('m.%s = 1', $type));
                    }

                    return $qb->orderBy('m.name', 'ASC');
                },
            ))
            ->add('size', 'integer', array(
                'required' => $options['force_required'],
                'label' => 'Grubość',
                'widget_addon_append' => [
                    'text' => 'cm',
                ],
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kraken\WarmBundle\Entity\Layer',
            'material_type' => 'for_wall_construction_layer',
            'force_required' => false,
            'material_label' => 'Materiał',
        ));
    }

    public function getName()
    {
        return 'layer';
    }
}
