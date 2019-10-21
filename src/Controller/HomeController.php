<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Announce;
use App\Entity\Location;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Form\SearchAnnounceType;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $em           = $this->getDoctrine()->getManager();
        $repoAnnounce = $em->getRepository(Announce::class);
        $repoLocation = $em->getRepository(Location::class);
        $repoUser     = $em->getRepository(User::class);
        $searchForm   = $this->createForm(SearchAnnounceType::class, null, [
            'action' => $this->generateUrl('home'),
            'method' => 'GET'
        ]);
  
        if ($request->isXmlHttpRequest()) {
            if ($searchForm->handleRequest($request)->isValid()) {
                $announce = $repoAnnounce->findForSearch($searchForm->getData());

                return $this->json([
                    'html' => $this->render('home/partials/_announcement_list.html.twig', [
                        "annonces"   => [
                            'scooter' => $searchForm->get('type')->getData() == Vehicle::SCOOTER ?
                                $announce : null,
                            'voiture' => $searchForm->get('type')->getData() == Vehicle::VOITURE ?
                                $announce : null
                        ],
                        "data" => [
                            'nbUser'     => $repoUser->findAll(),
                            'nbAnnounce' => $repoAnnounce->findAll(),
                            'nbLocation' => $repoLocation->findAll(),
                        ]
                    ])
                ]);
            }
        }

        return $this->render('home/index.html.twig', [
            "searchForm" => $searchForm->createView(),
            "annonces"   => [
                'scooter' => $repoAnnounce->findByVehicleType(Vehicle::SCOOTER),
                'voiture' => $repoAnnounce->findByVehicleType(Vehicle::VOITURE)
            ],
            "data" => [
                'nbUser'     => $repoUser->findAll(),
                'nbAnnounce' => $repoAnnounce->findAll(),
                'nbLocation' => $repoLocation->findAll(),
            ]
        ]);
    }

    /**
     * @Route("/eSwipe", name="eSwipe")
     * @Template("modal/_modal-form-e-swip.html.twig")
     * @param Request $request
     * @return Response
     */
    public function eSwipe(Request $request)
    {
        $searchForm  = $this->createForm(SearchAnnounceType::class, null, [
            'action' => $this->generateUrl('eSwipe'),
            'method' => 'POST'
        ])->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $em           = $this->getDoctrine()->getManager();

            return $this->render('announce/swipe.html.twig', [
                "searchForm" => $searchForm->createView(),
                "annonces"   => $em->getRepository(Announce::class)
                                   ->findForSearchSwipe($searchForm->getData())
            ]);
        }

        $searchForm = $searchForm->createView();

        return compact('searchForm');
    }

    /**
     * @Route("/vehicle/description/{announceId}", name="vehicleDescription")
     * @Template("modal/_modal-swipe.html.twig")
     * @param int $announceId
     * @param Request $request
     * @return Response
     */
    public function vehicleDescription(int $announceId, Request $request)
    {
        $em       = $this->getDoctrine()->getManager();
        $announce = $em->getRepository(Announce::class)->find($announceId);

        return compact('announce');
    }
}
