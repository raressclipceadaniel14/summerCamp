<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Car;
use App\Entity\Location;
use App\Entity\Station;
use App\Form\BookingFormType;
use App\Form\EditBookingFormType;
use App\Form\FilterFormType;
use App\Repository\StationsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MainController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filter_city = $form->getData()['cities'];
            $filter_charger = $form->getData()['type'];
            if ($filter_city == "-1" && $filter_charger == "-1") {
                return $this->render('index/index.html.twig', [
                    'message' => 'Select a city and/or a charging type',
                    'stations' => $doctrine->getRepository(Station::class)->findAll(),
                    'locations' => $doctrine->getRepository(Location::class)->findAll(),
                    'form' => $form->createView()
                ]);
            }
            elseif ($filter_city != "-1" && $filter_charger == "-1") {
                return $this->render('index/index.html.twig', [
                    'stations' => $doctrine->getRepository(Station::class)->findBy(['city' => $filter_city]),
                    'locations' => $doctrine->getRepository(Location::class)->findAll(),
                    'form' => $form->createView(),
                    'message' => 'Nonexistent'
                ]);
            }
            elseif ($filter_charger != "-1" && $filter_city == "-1") {
                return $this->render('index/index.html.twig', [
                    'stations' => $doctrine->getRepository(Station::class)->filterCharger($filter_charger),
                    'locations' => $doctrine->getRepository(Location::class)->findAll(),
                    'form' => $form->createView(),
                    'message' => 'Nonexistent'
                ]);
            }

            $locations = $doctrine->getRepository(Location::class)->findAll();
            $locations = array_values($locations);
            $stations = $doctrine->getRepository(Station::class)->filterCityCharger($filter_city, $filter_charger);
        }
        else
        {
            $locations = $doctrine->getRepository(Location::class)->findAll();
            $locations = array_values($locations);
            $stations = $doctrine->getRepository(Station::class)->findAll();
        }

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
            'stations' => $stations,
            'locations' => $locations,
            'message' => 'Nonexistent'
        ]);
    }

    #[Route("/profile", name: "profile", methods: ['GET'])]
    public function profile(ManagerRegistry $doctrine): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            return $this->render('index/index.html.twig', [
                'form' => $form = $this->createForm(FilterFormType::class)->createView(),
                'locations' => $doctrine->getRepository(Location::class)->findAll(),
                'title' => 'All locations',
                'message' => 'You can access profile only if you are logged in'
            ]);
        }

        return $this->render('profile/profile.html.twig', [
            'bookings' => $doctrine->getRepository(Booking::class)->getUserBookings($user),
            'message' => 'Nonexistent'
        ]);
    }

    #[Route("/profile", name: "addcar", methods: ['POST'])]
    public function profileAddCar(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->security->getUser();

        $license = $request->get('license');
        $type = $request->get('chargeType');

        if (!$license || $type == "er") {
            return $this->render('profile/profile.html.twig', [
                'bookings' => $doctrine->getRepository(Booking::class)->getUserBookings($user),
                'message' => 'Select charging type and type a valid license plate.'
            ]);
        }
        $cars = $doctrine->getRepository(Car::class)->findAll();
        foreach ($cars as $car) {
            if ($car->getLicensePlate() == $license) {
                return $this->render('profile/profile.html.twig', [
                    'bookings' => $doctrine->getRepository(Booking::class)->getUserBookings($user),
                    'message' => 'License plate is used'
                ]);
            }
        }

        $car = new Car();
        $car->setLicensePlate($license);
        $car->setChargeType("Type " . $type);
        $car->setUser($user);
        $doctrine->getManager()->persist($car);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('profile');
    }

    #[Route("/location/{name}", name: "location")]
    public function location(ManagerRegistry $doctrine, $name): Response
    {
        $location = $doctrine->getRepository(Location::class)->findOneBy(array('name' => $name));

        return $this->render('location/location.html.twig', [
            'location' => $location,
        ]);
    }

    #[Route("/station/{id}", name: "station")]
    public function station(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $station = $doctrine->getRepository(Station::class)->findOneBy(array('id' => $id));

        $form = $this->createForm(BookingFormType::class);
        $form->handleRequest($request);

        $bookings = $doctrine->getRepository(Booking::class)->getActiveBookings($id);


        if ($form->isSubmitted() && $form->isValid()) {
            $start = $form->getData()['start'];
            $end = $form->getData()['end'];
            $car = $doctrine->getRepository(Car::class)->findOneBy(array('license_plate' => $form->getData()['car']));

            if(!$car == null) {
                $overlap = $doctrine->getRepository(Booking::class)->getOverlappingBookings($station, $start, $end);
                if(count($overlap) > 0)
                {
                    return $this->render('station.html.twig', [
                        'station' => $station,
                        'form' => $form->createView(),
                        'bookings' => $bookings,
                        'message' => 'You are overlapping another booking',
                        'errors' => []
                    ]);
                }

                $overlap = $doctrine->getRepository(Booking::class)->getCarOverlap($car, $start, $end);
                if(count($overlap) > 0)
                {
                    return $this->render('station.html.twig', [
                        'station' => $station,
                        'form' => $form->createView(),
                        'bookings' => $bookings,
                        'message' => 'This cas has another booking in the same time',
                        'errors' => []
                    ]);
                }
            }

            $booking = new Booking();
            $booking->setChargestart($start);
            $booking->setChargeend($end);
            $booking->setStation($station);
            $booking->setCar($car);

            $doctrine->getManager()->persist($booking);
            $doctrine->getManager()->flush();
            $bookings = $doctrine->getRepository(Booking::class)->getActiveBookings($id);
        }

        return $this->render('station.html.twig', [
            'station' => $station,
            'form' => $form->createView(),
            'bookings' => $bookings,
            'message' => 'Nonexistent',
//            'reviews' => $doctrine->getRepository(Review::class)->findBy(array('station'=>$station), array('id'=>'DESC')),
            'errors' => []
        ]);
    }

    #[Route('/deleteBooking/{id}', name: "deleteBooking")]
    public function deleteBooking(ManagerRegistry $doctrine, $id): Response
    {
        $booking = $doctrine->getRepository(Booking::class)->findOneBy(array('id'=>$id));
        if(!$this->security->getUser() || $booking->getCar()->getUser() !== $this->security->getUser())
        {
            return $this->render('index/index.html.twig', [
                'form' => $form = $this->createForm(FilterFormType::class)->createView(),
                'locations' => $doctrine->getRepository(Location::class)->findAll(),
                'title' => 'All locations',
                'message' => 'Not your business!'
            ]);
        }

        $doctrine->getManager()->remove($booking);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('profile');
    }

    #[Route('/deleteCar/{id}', name: "deleteCar")]
    public function deleteCar(ManagerRegistry $doctrine, $id): Response
    {
        $car = $doctrine->getRepository(Car::class)->findOneBy(array('id'=>$id));
        if($this->security->getUser() !== $car->getUser())
        {
            return $this->render('index/index.html.twig', [
                'form' => $form = $this->createForm(FilterFormType::class)->createView(),
                'locations' => $doctrine->getRepository(Location::class)->findAll(),
                'title' => 'All locations',
                'message' => 'Not your business!'
            ]);
        }

        $doctrine->getManager()->remove($car);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('profile');
    }

    #[Route('/editBooking/{id}', name: "editBooking")]
    public function editBooking(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, Booking $booking): Response
    {
        if(!$this->security->getUser())
        {
            return $this->render('index/index.html.twig', [
                'form' => $form = $this->createForm(FilterFormType::class)->createView(),
                'locations' => $doctrine->getRepository(Location::class)->findAll(),
                'title' => 'All locations',
                'message' => 'The booking you are trying to edit is not yours!'
            ]);
        }

        $station = $booking->getStation();

        $form = $this->createForm(EditBookingFormType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Booking  $newbooking */
            $newbooking = $form->getData();
            $start = $newbooking->getChargeStart();
            $end = $newbooking->getChargeEnd();
            $car = $newbooking->getCar();

            $overlap = $doctrine->getRepository(Booking::class)->getCarOverlap($car, $start, $end);
            if(count($overlap) > 0)
            {
                if($overlap[0] != $booking || count($overlap) > 1)
                    return $this->render('editform.html.twig', [
                        'booking'=>$booking,
                        'form' => $form->createView(),
                        'message' => 'You have another booking for this car in the same time. Try deleting it before!',
                        'errors' => []
                    ]);
            }

            $bookings = $doctrine->getRepository(Booking::class)->getActiveBookings($station->getId());
            $overlap = $doctrine->getRepository(Booking::class)->getOverlappingBookings($station, $start, $end);
            if(count($overlap) > 0)
            {
                if($overlap[0] != $booking || count($overlap) > 1)
                    return $this->render('editform.html.twig', [
                        'booking'=>$booking,
                        'form' => $form->createView(),
                        'message' => 'There is another booking in that timeframe!',
                        'errors' => []
                    ]);
            }

            $booking->setChargestart($start); $booking->setChargeend($end); $booking->setCar($car);

            $doctrine->getManager()->persist($booking);
            $doctrine->getManager()->flush();
            return $this->redirectToRoute('profile');
        }
        return $this->render('editform.html.twig', [
            'booking'=>$booking,
            'form'=>$form->createView(),
            'message'=>'Nonexistent',
            'errors' => []
        ]);
    }

}
