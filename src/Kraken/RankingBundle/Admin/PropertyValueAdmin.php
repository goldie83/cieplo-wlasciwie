<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PropertyValueAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('type', null, ['label' => 'Typ'])
            ->add('name', null, ['label' => 'Nazwa'])
            ->add('value', 'choice', ['choices' => [-1 => 'Wada', 0 => 'Neutralna', 1 => 'Zaleta'], 'label' => 'Wartościowanie'])
            ->add('property', null, ['label' => 'Cecha'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
    }
}
