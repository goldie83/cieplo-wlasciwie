<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class NoticePrototypeAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('type', null, ['label' => 'Typ'])
            ->add('label', null, ['label' => 'Nazwa'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('label');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('label');
    }
}
