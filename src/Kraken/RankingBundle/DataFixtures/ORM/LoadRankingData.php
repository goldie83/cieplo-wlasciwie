<?php

namespace Kraken\RankingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kraken\RankingBundle\Entity\Boiler;
use Kraken\RankingBundle\Entity\Category;
use Kraken\RankingBundle\Entity\Manufacturer;

class LoadRankingData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $c1 = new Category();
        $c1->setName('Kotły zasypowe');
        $c1->setSingularName('Kocioł zasypowy');
        $c1->setSort(1);
        $manager->persist($c1);

        $c = new Category();
        $c->setName('Kotły górnego spalania');
        $c->setSingularName('Kocioł górnego spalania');
        $c->setSort(2);
        $c->setParent($c1);
        $manager->persist($c);

        $c = new Category();
        $c->setName('Kotły dolnego spalania');
        $c->setSingularName('Kocioł dolnego spalania');
        $c->setSort(3);
        $c->setParent($c1);
        $manager->persist($c);

        $c = new Category();
        $c->setName('Kotły górno-dolne');
        $c->setSingularName('Kocioł górno-dolny');
        $c->setSort(4);
        $c->setParent($c1);
        $manager->persist($c);

        $c2 = new Category();
        $c2->setName('Kotły podajnikowe');
        $c2->setSingularName('Kocioł podajnikowy');
        $c2->setSort(5);
        $manager->persist($c2);

        $c = new Category();
        $c->setName('Kotły retortowe');
        $c->setSingularName('Kocioł z palnikiem retortowym');
        $c->setSort(6);
        $c->setParent($c2);
        $manager->persist($c);

        $m = new Manufacturer();
        $m->setName('Ogniwo');
        $m->setWebsite('http://www.ogniwobiecz.com.pl');
        $manager->persist($m);

        $b = new Boiler();
        $b->setName('Ogniwo Eko Plus');
        $b->setMaterial('steel');
        $b->setCategory($c2);
        $b->setManufacturer($m);

        $manager->persist($b);
        $manager->flush();
    }
}
