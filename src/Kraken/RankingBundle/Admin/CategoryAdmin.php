<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CategoryAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'Nazwa kategorii'])
            ->add('singularName', null, ['label' => 'Nazwa w formie pojedynczej'])
            ->add('description', 'textarea', ['label' => 'Opis', 'required' => false])
            ->add('parent', null, ['label' => 'Kategoria nadrzędna'])
            ->add('sort', null, ['label' => 'Kolejność'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('parent')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('parent')
            ->add('description')
            ->add('sort')
        ;
    }
}
