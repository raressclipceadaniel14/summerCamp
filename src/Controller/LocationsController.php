<?php

namespace App\Controller;

use App\Form\FilterFormType;
use App\Repository\LocationsRepository;
use App\Repository\StationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationsController extends AbstractController
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
}
