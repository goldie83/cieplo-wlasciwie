<?php

namespace Kraken\RankingBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ReviewSummaryAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('rating', null, ['label' => 'Ogólna ocena'])
            ->add('reviewsNumber', null, ['label' => 'Liczba recenzji'])
            ->add('warrantyReviewsNumber', null, ['label' => 'Skorzystali z gwarancji'])
            ->add('comment', 'ckeditor', ['label' => 'Ogólny komentarz'])
            ->add('qualityRating', null, ['label' => 'Ocena jakości wykonania'])
            ->add('qualityComment', 'ckeditor', ['label' => 'Komentarz odnośnie jakości wykonania'])
            ->add('warrantyRating', null, ['label' => 'Ocena gwarancji i serwisu'])
            ->add('warrantyComment', 'ckeditor', ['label' => 'Komentarz odnośnie gwarancji i serwisu'])
            ->add('operationRating', null, ['label' => 'Ocena łatwości obsługi'])
            ->add('operationComment', 'ckeditor', ['label' => 'Komentarz odnośnie łatwości obsługi'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    protected function configureListFields(ListMapper $listMapper)
    {
    }
}
