<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\Station;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StationsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++){
            $station = new Station();
            $station->setLocation($manager->getRepository(Location::class)->findOneBy(['Name' => 'Auchan'.$i]));
            if ($i % 2 == 0) {
                $station->setType('type 2');
            }
            else {
                $station->setType('type 1');
            }

            $station->setPower('34');
            $manager->persist($station);
        }

        $manager->flush();
    }
}
