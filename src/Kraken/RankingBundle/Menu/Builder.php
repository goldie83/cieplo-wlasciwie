<?php

namespace Kraken\RankingBundle\Menu;

use Knp\Menu\FactoryInterface;

class Builder
{
    public function createMainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'navbar' => true,
        ));

        $menu->addChild('Start', array('route' => 'homepage'));

        return $menu;
    }
}