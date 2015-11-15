<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ChangeAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('boiler', null, ['label' => 'Kocioł'])
            ->add('date', 'date', ['label' => 'Data zmiany'])
            ->add('content', null, ['label' => 'Opis zmiany'])
            ->add('oldRating', null, ['label' => 'Poprzednia ocena'])
            ->add('newRating', null, ['label' => 'Nowa ocena'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('boiler', null, ['label' => 'Kocioł'])
            ->add('date', null, ['label' => 'Data zmiany'])
            ->add('content', null, ['label' => 'Opis zmiany'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('boiler', null, ['label' => 'Kocioł'])
            ->addIdentifier('date', null, ['label' => 'Data zmiany'])
            ->add('content', null, ['label' => 'Opis zmiany'])
        ;
    }
}
