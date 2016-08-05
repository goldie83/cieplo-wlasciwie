<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ReviewAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('boiler', null, ['label' => 'Kocioł'])
            ->add('rating', null, ['label' => 'Ogólna ocena'])
            ->add('comment', 'textarea', ['label' => 'Ogólny komentarz', 'required' => false])
            ->add('qualityRating', null, ['label' => 'Ocena jakości wykonania'])
            ->add('qualityComment', 'textarea', ['label' => 'Komentarz odnośnie jakości wykonania', 'required' => false])
            ->add('warrantyRating', null, ['label' => 'Ocena serwisu i gwarancji'])
            ->add('warrantyComment', 'textarea', ['label' => 'Komentarz odnośnie gwarancji i serwisu', 'required' => false])
            ->add('operationRating', null, ['label' => 'Ocena obsługi'])
            ->add('operationComment', 'textarea', ['label' => 'Komentarz odnośnie obsługi', 'required' => false])
            ->add('email', null, ['label' => 'E-mail'])
            ->add('ip', null, ['label' => 'IP'])
            ->add('userAgent', null, ['label' => 'Klient'])
            ->add('accepted', null, ['label' => 'Zaakceptowany'])
            ->add('revoked', null, ['label' => 'Odwołany'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('boiler')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('boiler')
            ->add('email')
            ->add('created')
            ->add('accepted')
            ->add('revoked')
        ;
    }
}
