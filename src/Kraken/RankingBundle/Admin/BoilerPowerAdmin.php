<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BoilerPowerAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('boiler', null)
            ->add('power', null, ['label' => 'Moc'])
            ->add('fuelType', null, ['label' => 'Paliwo'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
//         $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
//         $listMapper->addIdentifier('name');
    }
}
