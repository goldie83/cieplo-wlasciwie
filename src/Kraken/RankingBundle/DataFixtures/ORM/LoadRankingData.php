<?php

namespace Kraken\RankingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kraken\RankingBundle\Entity\Boiler;
use Kraken\RankingBundle\Entity\BoilerProperty;
use Kraken\RankingBundle\Entity\Category;
use Kraken\RankingBundle\Entity\Manufacturer;
use Kraken\RankingBundle\Entity\Property;

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


        $p1 = new Property;
        $p1->setPositive(true);
        $p1->setLabel('Sterownik adaptacyjny');
        $p1->setContent('Sam dobiera parametry pracy, bez konieczności ręcznych nastaw');
        $manager->persist($p1);

        $p2 = new Property;
        $p2->setPositive(true);
        $p2->setLabel('Palnik II generacji');
        $p2->setContent('Jest w stanie spalać gorsze, spiekające węgle oraz miał');
        $manager->persist($p2);

        $p3 = new Property;
        $p3->setPositive(true);
        $p3->setLabel('Dopuszczenie do układu zamkniętego');
        $p3->setContent('Kocioł posiada potwierdzone pisemnie zezwolenie na montaż w układzie zamkniętym');
        $manager->persist($p3);


        $m = new Manufacturer();
        $m->setName('Ogniwo');
        $m->setWebsite('http://www.ogniwobiecz.com.pl');
        $manager->persist($m);

        $b = new Boiler();
        $b->setName('Ogniwo Eko Plus');
        $b->setCategory($c3);
        $b->setManufacturer($m);

        $bp1 = new BoilerProperty;
        $bp1->setProperty($p1);
        $bp1->setBoiler($b);

        $bp2 = new BoilerProperty;
        $bp2->setProperty($p2);
        $bp2->setBoiler($b);

        $manager->persist($b);
        $manager->persist($bp1);
        $manager->persist($bp2);

        $manager->flush();
    }
}
