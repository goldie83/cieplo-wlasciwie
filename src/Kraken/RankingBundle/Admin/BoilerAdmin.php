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
            ->add('material', 'choice', ['choices' => ['steel' => 'Stal', 'cast_iron' => 'Żeliwo'], 'label' => 'Materiał konstrukcyjny wymiennika'])
            ->add('imageFile', 'vich_file', ['label' => 'Główny obrazek', 'required' => false])
            ->add('crossSectionFile', 'vich_file', ['label' => 'Przekrój', 'required' => false])
            ->add('propertyValues', 'sonata_type_collection', ['label' => 'Lista cech'], [
                'edit' => 'inline',
                'inline' => 'table',
            ])
            ->add('boilerPowers', 'sonata_type_collection', ['label' => 'Lista mocy', 'by_reference' => false], [
                'edit' => 'inline',
                'inline' => 'table',
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
            ->add('content', 'ckeditor', ['label' => 'Artykuł'])
            ->add('ratingExplanation', 'ckeditor', ['label' => 'Wyjaśnienie oceny'])
            ->add('rating', null, ['label' => 'Ocena'])
            ->add('normClass', null, ['label' => 'Klasa wg PN-EN 303-5:2012'])
            ->add('typicalModelPower', null, ['label' => 'Moc wzorcowego modelu'])
            ->add('typicalModelExchanger', null, ['label' => 'Pow. wymiennika'])
            ->add('typicalModelCapacity', null, ['label' => 'Poj. zasypowa'])
            ->add('typicalModelPrice', null, ['label' => 'Cena'])
            ->add('warranty', null, ['label' => 'Długość gwarancji (miesiące)'])
            ->add('userManual', null, ['label' => 'Link do DTR'])
            ->add('forClosedSystem', null, ['label' => 'Układ zamknięty'])
            ->add('changes', 'sonata_type_collection', ['label' => 'Log zmian'], [
                'edit' => 'inline',
                'inline' => 'table',
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Model'])
            ->add('category', null, ['label' => 'Rodzaj'])
            ->add('manufacturer', null, ['label' => 'Producent'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, ['label' => 'Model'])
            ->add('category', null, ['label' => 'Rodzaj'])
            ->add('manufacturer', null, ['label' => 'Producent'])
        ;
    }

    public function toString($object)
    {
        return $object instanceof Boiler
            ? $object->getName()
            : 'Kocioł';
    }
}
