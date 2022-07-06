<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LocationsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $location = new Location();
            $location->setName('Auchan'.$i);
            $location->setTotalSpots('2');
            $location->setLat('23.5');
            $location->setLongitude('45.3');
            $location->setPrice('1.2');
            if ($i % 3 == 0){
                $location->setCity('Craiova');
            }
            elseif ($i % 3 == 1) {
                $location->setCity('Bucuresti');
            }
            elseif ($i % 3 == 2) {
                $location->setCity('Cluj');
            }
            $manager->persist($location);
        }

        $manager->flush();
    }
}
