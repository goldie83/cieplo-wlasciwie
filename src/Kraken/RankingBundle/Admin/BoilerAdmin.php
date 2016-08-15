<?php

namespace Kraken\RankingBundle\Admin;

use Kraken\RankingBundle\Entity\Boiler;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BoilerAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', ['label' => 'Pełna nazwa'])
            ->add('category', null, ['label' => 'Rodzaj kotła'])
            ->add('manufacturer', null, ['label' => 'Producent'])
            ->add('manufacturerSite', null, ['label' => 'Strona producenta z tym kotłem', 'required' => true])
            ->add('rejected', null, ['label' => 'Do salonu odrzuconych'])
            ->add('material', 'choice', ['choices' => ['steel' => 'Stal', 'cast_iron' => 'Żeliwo'], 'label' => 'Materiał konstrukcyjny wymiennika'])
            ->add('imageFile', 'vich_file', ['label' => 'Główny obrazek', 'required' => false])
            ->add('crossSectionFile', 'vich_file', ['label' => 'Przekrój', 'required' => false])
            ->add('boilerPowers', 'sonata_type_collection', ['label' => 'Lista mocy', 'by_reference' => false], [
                'edit' => 'inline',
                'inline' => 'table',
                'allow_delete' => true,
            ])
            ->add('boilerFuelTypes', 'sonata_type_collection', ['label' => 'Lista paliw'], [
                'edit' => 'inline',
                'inline' => 'table',
            ])
            ->add('notices', 'sonata_type_collection', ['label' => 'Zalety/wady'], [
                'edit' => 'inline',
                'inline' => 'table',
            ])
            ->add('lead', 'ckeditor', ['label' => 'Wstęp'])
            ->add('content', 'ckeditor', [
                'label' => 'Artykuł',
                'config' => array(
                'extraPlugins' => 'lineutils,widget,image2',
                ),
                'plugins' => array(
                    'image2' => array(
                        'path' => 'bundles/sonataformatter/vendor/ckeditor/plugins/image2/',
                        'filename' => 'plugin.js',
                    ),
                    'widget' => array(
                        'path' => 'bundles/sonataformatter/vendor/ckeditor/plugins/widget/',
                        'filename' => 'plugin.js',
                    ),
                    'lineutils' => array(
                        'path' => 'bundles/sonataformatter/vendor/ckeditor/plugins/lineutils/',
                        'filename' => 'plugin.js',
                    ),
                ),
            ])
            ->add('ratingExplanation', 'ckeditor', ['label' => 'Wyjaśnienie oceny'])
            ->add('rating', null, ['label' => 'Ocena'])
            ->add('normClass', null, ['label' => 'Klasa wg PN-EN 303-5:2012'])
            ->add('typicalModelPower', null, ['label' => 'Moc wzorcowego modelu'])
            ->add('typicalModelExchanger', null, ['label' => 'Pow. wymiennika'])
            ->add('typicalModelCapacity', null, ['label' => 'Poj. zasypowa / zasobnika (l)'])
            ->add('typicalModelPrice', null, ['label' => 'Cena'])
            ->add('warranty', null, ['label' => 'Długość gwarancji (miesiące)'])
            ->add('userManual', null, ['label' => 'Link do DTR'])
            ->add('forClosedSystem', null, ['label' => 'Układ zamknięty'])
            ->add('changes', 'sonata_type_collection', ['label' => 'Log zmian'], [
                'edit' => 'inline',
                'inline' => 'table',
            ])
            ->add('reviewSummary', 'sonata_type_admin', ['label' => 'Podsumowanie ocen'])
            ->add('published')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Model'])
            ->add('category', null, ['label' => 'Rodzaj'])
            ->add('manufacturer', null, ['label' => 'Producent'])
            ->add('published', null, ['label' => 'Opublikowany'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, ['label' => 'Model'])
            ->add('category', null, ['label' => 'Rodzaj'])
            ->add('manufacturer', null, ['label' => 'Producent'])
            ->add('published', null, ['label' => 'Opublikowany'])
        ;
    }

    public function toString($object)
    {
        return $object instanceof Boiler
            ? $object->getName()
            : 'Kocioł';
    }

    public function prePersist($boiler)
    {
        foreach ($boiler->getBoilerPowers() as $bp) {
            $bp->setBoiler($boiler);
        }

        foreach ($boiler->getBoilerFuelTypes() as $bf) {
            $bf->setBoiler($boiler);
        }

        foreach ($boiler->getChanges() as $c) {
            $c->setBoiler($boiler);
        }

        foreach ($boiler->getNotices() as $n) {
            $n->setBoiler($boiler);
        }
    }

    public function preUpdate($boiler)
    {
        foreach ($boiler->getBoilerPowers() as $bp) {
            $bp->setBoiler($boiler);
        }

        $boiler->setBoilerPowers($boiler->getBoilerPowers());

        foreach ($boiler->getBoilerFuelTypes() as $bf) {
            $bf->setBoiler($boiler);
        }

        $boiler->setBoilerFuelTypes($boiler->getBoilerFuelTypes());

        foreach ($boiler->getChanges() as $c) {
            $c->setBoiler($boiler);
        }

        $boiler->setChanges($boiler->getChanges());

        foreach ($boiler->getNotices() as $n) {
            $n->setBoiler($boiler);
        }

        $boiler->setNotices($boiler->getNotices());
    }
}
