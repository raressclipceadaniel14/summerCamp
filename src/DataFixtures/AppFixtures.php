<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Car;
use App\Entity\Location;
use App\Entity\Station;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //LOCATION FIXTURE

        for ($i = 0; $i < 20; $i++) {
            $location = new Location();
            $location->setName('Auchan' . $i);
            $location->setTotalSpots('2');
            if ($i == 1) {
                $location->setLat('44.817930');
                $location->setLongitude('24.885550');
            }
            elseif ($i == 2) {
                $location->setLat('45.755480');
                $location->setLongitude('21.234650');
            }
            elseif ($i == 3) {
                $location->setLat('44.477774');
                $location->setLongitude('26.103765');
            }
            elseif ($i == 4) {
                $location->setLat('45.021881');
                $location->setLongitude('23.264071');
            }
            elseif ($i == 5) {
                $location->setLat('43.993640');
                $location->setLongitude('22.928840');
            }
            else
            {
                $location->setLat('45.3');
                $location->setLongitude('23.5');
            }
            $location->setPrice('1.2');
            if ($i % 3 == 0) {
                $location->setCity('Craiova');
            } elseif ($i % 3 == 1) {
                $location->setCity('Bucuresti');
            } elseif ($i % 3 == 2) {
                $location->setCity('Cluj');
            }
            $manager->persist($location);
        }
        $manager->flush();

        //STATION FIXTURE

        for ($i = 0; $i < 20; $i++) {
            $station = new Station();
            $station->setLocation($manager->getRepository(Location::class)->findOneBy(['Name' => 'Auchan' . $i]));
            if ($i % 2 == 0) {
                $station->setType('type 2');
            } else {
                $station->setType('type 1');
            }

            $station->setPower('34');
            $manager->persist($station);
        }
        $manager->flush();

        //USER FIXTURE

//        for ($i = 0; $i < 20; $i++) {
//            $user = new User();
//            $user->setName('Marcel' . $i);
//            $user->setEmail('marcel@gmail.com');
//            $user->setCity('Craiova');
//            $manager->persist($user);
//        }
//        $manager->flush();

        //CAR FIXTURE

//        for ($i = 0; $i < 20; $i++) {
//            $car = new Car();
//            $car->setUser($manager->getRepository(User::class)->findOneBy(['name' => 'Marcel' . $i]));
//            $car->setLicensePlate('DJ99BBB');
//            $car->setChargeType('type 1');
//            $manager->persist($car);
//        }
//        $manager->flush();

        //BOOKING FIXTURE

//        for ($i = 0; $i < 20; $i++)
//        {
//            $booking = new Booking();
//            $booking->setCarId($manager->getRepository(Car::class)->findOneBy(['license_plate' => 'DJ99BBB']));
//            $booking->setStationId($manager->getRepository(Station::class)->findOneBy(['power' => '34']));
//            $booking->setChargeStart(new \DateTime("+$i day"));
//            $booking->setChargeEnd(new \DateTime("+$i day +1 hour"));
//            $manager->persist($booking);
//        }
//        $manager->flush();
    }
}
