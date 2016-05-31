<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class NoticeAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('noticePrototype', 'sonata_type_admin', ['label' => 'Prototyp'])
            ->add('importance', null, ['label' => 'Ważność'])
            ->add('valuation', 'choice', [
                'label' => 'Rodzaj',
                'choices' => [
                    'advantage' => 'Zaleta',
                    'unknown' => 'Niewiadoma',
                    'disadvantage' => 'Wada',
                ],
            ])
            ->add('label', null, ['label' => 'Nazwa'])
            ->add('content', null, ['label' => 'Opis'])
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
