<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BoilerPropertyAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('boiler', null, ['label' => 'Kocioł'])
            ->add('property', null, ['label' => 'Cecha'])
            ->add('label', null, ['label' => 'Alternatywna nazwa'])
            ->add('content', null, ['label' => 'Alternatywny opis'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('property');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('property');
    }
}
