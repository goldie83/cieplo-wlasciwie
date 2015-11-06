<?php

namespace Kraken\RankingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kraken\RankingBundle\Entity\User;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Jozin Bazin');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setUsername('admin+dev@czysteogrzewanie.pl');
        $user->setEmail('admin+dev@czysteogrzewanie.pl');
        $user->setPlainPassword('haszlo');
        $user->setEnabled(true);

        $manager->persist($user);
        $manager->flush();
    }
}
