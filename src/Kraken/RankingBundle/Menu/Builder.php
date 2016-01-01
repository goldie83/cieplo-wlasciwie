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
        $menu->addChild('O rankingu', ['route' => 'ranking_about']);
        $menu->addChild('Kryteria', ['route' => 'ranking_criteria']);
        $menu->addChild('Kotły zasypowe', ['route' => 'ranking_boiler_category', 'routeParameters' => ['category' => 'kotly-zasypowe']]);
        $menu->addChild('Kotły podajnikowe', ['route' => 'ranking_boiler_category', 'routeParameters' => ['category' => 'kotly-podajnikowe']]);
        $menu->addChild('Salon odrzuconych', ['route' => 'ranking_rejected']);

        return $menu;
    }
}
