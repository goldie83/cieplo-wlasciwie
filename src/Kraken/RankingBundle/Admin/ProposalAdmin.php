<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ProposalAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('url', null, ['label' => 'Adres'])
            ->add('content', null, ['label' => 'Opis'])
            ->add('email', null, ['label' => 'Autor'])
            ->add('boiler', null, ['label' => 'Kocioł'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('url')
            ->add('content')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('url')
            ->add('content')
            ->add('email')
            ->add('boiler')
        ;
    }
}
