<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Car;
use App\Entity\Station;
use App\Form\BookingFormType;
use App\Form\FilterFormType;
use App\Repository\BookingsRepository;
use App\Repository\StationsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @param Request $request
     * @param StationsRepository $stationsRepository
     * @return Response
     */
    #[Route('/', name: 'app_stations')]
    public function stations(Request $request, StationsRepository $stationsRepository): Response
    {
        $form = $this->createForm(FilterFormType::class);
        $form->handleRequest($request);

        $stationCriteria = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $stationCriteria = $form->getData();
        }

        $stations = $stationsRepository->findStations($stationCriteria['Type'] ?? '', $stationCriteria['City'] ?? '');

        return $this->render('stations/stations.html.twig', [
            'controller_name' => 'LocationController',
            'stations' => $stations,
            'location_filter' => $form->createView(),
        ]);
    }

    #[Route("/station/{id}", name: "station")]
    public function Station(Request $request, ManagerRegistry $doctrine, $id) : Response
    {
        $station = $doctrine->getRepository(Station::class)->findOneBy(array('id' => $id));

        $form = $this->createForm(BookingFormType::class);
        $form->handleRequest($request);

        $bookings = $doctrine->getRepository(Booking::class)->getActiveBookings($id);

        if($form->isSubmitted() && $form->isValid()){
            $start = $form->getData()['start'];
            $end = $form->getData()['end'];
            $car = $doctrine->getRepository(Car::class)->findOneBy(array('license_plate'=>$form->getData()['car']));

            $totalsecdifference = strtotime($end->format('Y-m-d h:i:s')) - strtotime($start->format('Y-m-d h:i:s'));
            if($totalsecdifference <= 0 || $totalsecdifference > 7200)
            {
                return $this->render('station.html.twig', [
                    'station'=>$station,
                    'form'=>$form->createView(),
                    'bookings'=>$bookings,
                    'message'=>"Start time must be less than end time and reservations can't exceed an hour and a half."
                ]);
            }

            if($start < new \DateTimeImmutable())
            {
                return $this->render('station.html.twig', [
                    'station'=>$station,
                    'form'=>$form->createView(),
                    'bookings'=>$bookings,
                    'message'=>'Booking must not be made in the past.'
                ]);
            }

            foreach($bookings as $booking)
            {
                $bstart = $booking->getChargeStart();
                $bend = $booking->getChargeEnd();
                if(($bstart <= $start && $bend >= $start) || ($bstart <= $end && $bend >= $end))
                {
                    return $this->render('station.html.twig', [
                        'station'=>$station,
                        'form'=>$form->createView(),
                        'bookings'=>$bookings,
                        'message'=>'There is another booking in that timeframe!'
                    ]);
                }
            }

            if($car->getChargeType() != $station->getType())
            {
                return $this->render('station.html.twig', [
                    'station'=>$station,
                    'form'=>$form->createView(),
                    'bookings'=>$bookings,
                    'message'=>'This car has a different charging type from the station. Please select a different station or a car with charging ' . $station->getType(),
                ]);
            }

            $booking = new Booking();
            $booking->setChargestart($start);
            $booking->setChargeend($end);
            $booking->setStationId($station);
            $booking->setCarId($car);

            $doctrine->getManager()->persist($booking);
            $doctrine->getManager()->flush();
            $bookings = $doctrine->getRepository(Booking::class)->getActiveBookings($id);
        }

        return $this->render('station.html.twig', [
            'station'=>$station,
            'form'=>$form->createView(),
            'bookings'=>$bookings,
            'message'=>'Nonexistent'
        ]);
    }
}
