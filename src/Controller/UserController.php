<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Location;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\LocationRepository;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Nzo\UrlEncryptorBundle\UrlEncryptor\UrlEncryptor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    private $encryptor;

    public function __construct(UrlEncryptor $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $formRegister = $this->createForm(RegistrationFormType::class, $user = new User());
        $formRegister->handleRequest($request);

        if ($formRegister->isSubmitted() && $formRegister->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $formRegister->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user/register.html.twig', [
            'controller_name' => 'UserController',
            'registrationForm' => $formRegister->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('user/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        return true;
    }

    /**
     * @Route("/history", name="history")
     */
    public function history()
    {
        /** @var User $userConnected */
        $userConnected = $this->getUser();

        if (!empty($userConnected)) {
            $locationPast    = $locationFuture = $locationDate = "";
            $idUserConnected = $userConnected->getId();
            $em              = $this->getDoctrine()->getManager();
            /** @var LocationRepository $repoLocation */
            $repoLocation   = $em->getRepository(Location::class);
            $locationPast   = $repoLocation->getLocationPast($idUserConnected);
            $locationFuture = $repoLocation->getLocationFutur($idUserConnected);
            $locationDate   = $repoLocation->getLocationDate($idUserConnected);

            return $this->render('user/history.html.twig', [
                'locationPast'  => $locationPast,
                'locationFutur' => $locationFuture,
                'locationDate'  => $locationDate,
            ]);
        }

        return $this->redirectToRoute('user_login');
    }

    /**
     * @Route("/account", name="account")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function account(Request $request)
    {
        /** @var User $userConnected */
        $userConnected = $this->getUser();

        if (!empty($userConnected)) {
            $idUser = $userConnected->getId();
            $repository = $this->getDoctrine()->getRepository(User::class);
            $infoUser = $repository->find($idUser);
            $formUser = $this->createForm(UserType::class, $userConnected);
            $formUser->handleRequest($request);

            if ($formUser->isSubmitted() && $formUser->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                return $this->render('user/account.html.twig', [
                    'infoUser' => $infoUser,
                    'formUser' => $formUser->createView(),
                ]);
            }

            return $this->render('user/account.html.twig', [
                'infoUser' => $infoUser,
                'formUser' => $formUser->createView(),
            ]);
        }

        return $this->redirectToRoute('user_login');
    }

    /**
     * @Route("/end/location/{locationId}", name="endLocation")
     * @Template("modal/_modal_comment.html.twig")
     * @ParamDecryptor(params={"locationId"})
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param string $locationId
     * @return array|RedirectResponse
     * @throws \Exception
     */
    public function endLocation(Request $request, \Swift_Mailer $mailer, string $locationId)
    {
        $commentForm  = $this->createForm(CommentType::class, null, [
            'action' => $this->generateUrl(
                'user_endLocation',
                [ 'locationId' => $this->encryptor->encrypt($locationId)]
            ),
            'method' => 'POST'
        ])->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em           = $this->getDoctrine()->getManager();
            $repoLocation = $em->getRepository(Location::class);

            /** @var Location|null $location */
            if ($location = $repoLocation->find($locationId)) {
                if ($content = $commentForm->get('comment')->getData()) {
                    /** @var User $user */
                    $user    = $this->getUser();
                    $comment = new Comment();
                    $comment->setAnnounce($location->getAnnounce())
                            ->setUser($user)
                            ->setRate($request->request->get('rate'))
                            ->setContent($content);
                    $em->persist($comment);
                }
                $location->getAnnounce()->setEnable(true);
                $location->setReturned(true)
                         ->setReturnedAt(new \DateTime());
                $em->persist($location);
                $em->flush();

                if ($location->getReturnedAt() > $location->getEndDate()) {
                    $message = (new \Swift_Message('[ALERT]E-motion: Vehicule rendu en retard'))
                        ->setFrom('admin@e-motion.fr')
                        ->setTo('admin@e-motion.fr')
                        ->setBody(
                            $this->renderView(
                                'user/partials/_email.html.twig',
                                [
                                    'user'    => $location->getUser(),
                                    'vehicle' => $location->getAnnounce()->getVehicle()
                                ]
                            ),
                            'text/html'
                        )
                    ;

                    $mailer->send($message);
                }
            }

            return $this->redirectToRoute('user_history');
        }

        $commentForm = $commentForm->createView();

        return compact('commentForm');
    }
}
