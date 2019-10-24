<?php

namespace App\Controller;

use App\Form\SearchAnnounceType;
use App\Repository\AnnounceRepository;
use App\Repository\VehicleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use App\Entity\Announce;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Entity\Location;
use App\Form\AnnouncementType;
use App\Form\DateLocationType;
use App\Form\RentalType;
use Nzo\UrlEncryptorBundle\UrlEncryptor\UrlEncryptor;

/**
 * Class AnnounceController
 * @package App\Controller
 * @Route("/annonce")
 */
class AnnounceController extends AbstractController
{
    private $encryptor;

    public function __construct(UrlEncryptor $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $em           = $this->getDoctrine();
        /** @var AnnounceRepository $repoAnnounce */
        $repoAnnounce = $em->getRepository(Announce::class);
        $searchForm   = $this->createForm(SearchAnnounceType::class)
                             ->handleRequest($request);
        $annonces     = $repoAnnounce->findAll();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data     = $searchForm->getData();
            $annonces = $repoAnnounce->findForSearch($data);
        }

        return $this->render('announce/index.html.twig', [
            "searchForm" => $searchForm->createView(),
            "annonces"   => $annonces,
        ]);
    }

    /**
     * @Route("/detail/{id}", name="detail_announce")
     * @param Request $request
     * @param Security $security
     * @param Announce $announce
     * @return Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function detailAction(Request $request, Security $security, Announce $announce)
    {
        $form = $this->createForm(DateLocationType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date       = $form->getData();

            if ($this->isCsrfTokenValid('location_item', $request->request->get('date_location')['_token'])) {
                $hours      = $this->diffHours($date['stopDateTime'], $date['startDateTime']);
                $price      = round(($announce->getPrice() /24) * $hours, 2);

                if ($form->get('reduction')->isClicked()) {
                    $price -= 5;
                }

                $priceTotal = $this->eurToCents($price);

                if ($this->isGranted("IS_AUTHENTICATED_FULLY")) {
                    $em         = $this->getDoctrine()->getManager();
                    /** @var User $user */
                    $user       = $security->getUser();
                    $location   = new Location();
                    $location->setUser($user)
                             ->setAnnounce($announce)
                             ->setStartDate($date['startDateTime'])
                             ->setEndDate($date['stopDateTime'])
                             ->setPricePaid($price);

                    if ($form->get('reduction')->isClicked()) {
                        $user->setLoyaltyPoints($this->getLoyaltyPoint($user));
                    }

                    $em->persist($location);
                    $em->flush();

                    $stripe = new Stripe();
                    $stripe::setApiKey('sk_test_jWWKdFyljvdnfJbjevs74kQH000tfdnAdA');

                    $session = Session::create([
                        'payment_method_types' => ['card'],
                        'line_items' => [[
                            'name'        => $announce->getVehicle()->getBrand()
                                     . " " . $announce->getVehicle()->getModel(),
                            'description' => $announce->getDescription(),
                            'amount'      => $priceTotal,
                            'currency'    => 'eur',
                            'quantity'    => 1,
                        ]],
                        'success_url' => 'http://127.0.0.1:8000'. $this->generateUrl('successPayment', [
                            'locationId' => $this->encryptor->encrypt($location->getId())
                        ]),
                        'cancel_url' => 'https://example.com/cancel',
                    ]);

                    return $this->render('payment/index.html.twig', [
                        'controller_name' => 'PaymentController',
                        'sessionId' => $session['id']
                    ]);
                } else {
                    return $this->redirectToRoute('user_login');
                }
            }
        }

        return $this->render('announce/detail.html.twig', [
            "annonce" => $announce,
            "form" => $form->createView()
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route(
     *     "/add/vehicle",
     *     name="vehicleAnnounce"
     * )
     * @Template("announce/rental.html.twig")
     * @param Request $request
     * @return array
     */
    public function addVehicleAction(Request $request)
    {
        $form = $this->createForm(RentalType::class, new Vehicle(), [
            'action' => $this->generateUrl('announcement'),
            'method' => 'POST',
            'attr' => [
                'id'     => 'rental'
            ]
        ])->handleRequest($request);

        $form = $form->createView();

        return compact('form');
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route(
     *     "/add/announcement",
     *     name="announcement"
     * )
     * @Template("announce/partials/_announcement.html.twig")
     * @param Request $request
     * @param Security $security
     * @return array|RedirectResponse
     * @throws \Exception
     */
    public function addAnnouncementAction(Request $request, Security $security)
    {
        $form = $this->createForm(AnnouncementType::class, null, [
            'action'  => $this->generateUrl('announcement'),
            'method'  => 'POST',
            'vehicle' => $request->request->get('rental') ? $request->request->get('rental') : null,
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->isCsrfTokenValid('announcement_item', $request->request->get('announcement')['_token'])
                && $this->isCsrfTokenValid('rental_item', $data['vehicle']['_token'])) {
                $em          = $this->getDoctrine()->getManager();
                $announce    = new Announce();
                /** @var VehicleRepository $repoVehicle */
                $repoVehicle = $em->getRepository(Vehicle::class);
                /** @var User $user */
                $user        = $security->getUser();

                /** @var Vehicle $vehicle */
                if (!$vehicle = $repoVehicle->findOneBy([
                    'matriculation' => $data['vehicle']['matriculation'],
                    'user'          => $user,
                    'type'          => $data['vehicle']['type'],
                ])) {
                    $vehicle  = new Vehicle();
                    $door     = array_key_exists('door', $data['vehicle']) ? $data['vehicle']['door'] : null;
                    $place    = array_key_exists('place', $data['vehicle']) ? $data['vehicle']['door'] : null;
                    $photo    = array_key_exists('photo', $data['vehicle']) ?
                                $data['vehicle']['photo'] : $vehicle->getPhoto();

                    $vehicle->setType($data['vehicle']['type'])
                            ->setModel($data['vehicle']['model'])
                            ->setBrand($data['vehicle']['brand'])
                            ->setMatriculation($data['vehicle']['matriculation'])
                            ->setKm($data['vehicle']['km'])
                            ->setYear(new \DateTime($data['vehicle']['year']))
                            ->setDoor($door)
                            ->setPlace($place)
                            ->setAutonomy($data['vehicle']['autonomy'])
                            ->setUser($user)
                            ->setPhoto($photo);
                    $em->persist($vehicle);
                }

                $announce->setUser($user)
                         ->setAddress($data['address'])
                         ->setCity($data['city'])
                         ->setDescription($data['description'])
                         ->setEnable(true)
                         ->setPrice($data['price'])
                         ->setZipcode($data['zipcode'])
                         ->setVehicle($vehicle);
                $em->persist($announce);
                $em->flush();

                return $this->redirectToRoute("home");
            }
        }

        $form = $form->createView();

        return compact('form');
    }


    private function diffHours(\DateTime $dt2, \DateTime $dt1)
    {
        $diff  = $dt2->diff($dt1);
        $hours = $diff->h;
        $hours = $hours + ($diff->days*24);
        return $hours;
    }

    private function eurToCents($price)
    {
        return $price * 100;
    }

    /**
     * @param User $user
     * @return int|mixed
     */
    public function getLoyaltyPoint(User $user)
    {
        $loyaltyPoints = $user->getLoyaltyPoints();
        $loyaltyPoints -= 100;

        return $loyaltyPoints;
    }
}
