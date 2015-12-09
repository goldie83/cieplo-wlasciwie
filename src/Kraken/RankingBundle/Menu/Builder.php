<?php

namespace Kraken\RankingBundle\Menu;

use Knp\Menu\FactoryInterface;

class Builder
{
    public function createMainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', [
            'navbar' => true,
        ]);

        $menu->addChild('Start', ['route' => 'ranking_homepage']);
        $menu->addChild('Kotły zasypowe', ['route' => 'ranking_boiler_category', 'routeParameters' => ['category' => 'kotly-zasypowe']]);
        $menu->addChild('Kotły podajnikowe', ['route' => 'ranking_boiler_category', 'routeParameters' => ['category' => 'kotly-podajnikowe']]);

        return $menu;
    }
}
