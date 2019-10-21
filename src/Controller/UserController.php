<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Location;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Nzo\UrlEncryptorBundle\UrlEncryptor\UrlEncryptor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request,UserPasswordEncoderInterface $passwordEncoder)
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
     * @return \Symfony\Component\HttpFoundation\Response
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
        $userConnected = $this->getUser();
        if(!empty($userConnected)) {
            $locationPast = $locationFutur = $locationDate = "";
            $idUserConnected = $userConnected->getId();
            $repository = $this->getDoctrine()->getRepository(Location::class);
            $locationPast = $repository->getLocationPast($idUserConnected);
            $locationFutur = $repository->getLocationFutur($idUserConnected);
            $locationDate = $repository->getLocationDate($idUserConnected);

            return $this->render('user/history.html.twig', [
                'locationPast' => $locationPast,
                'locationFutur' => $locationFutur,
                'locationDate' => $locationDate,
            ]);
        }

        return $this->redirectToRoute('user_login');
    }

    /**
     * @Route("/account", name="account")
     */
    public function account(Request $request){
        $userConnected = $this->getUser();
        if(!empty($userConnected)){
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
     * @param Request $request
     * @param $locationId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @ParamDecryptor(params={"locationId"})
     */
    public function endLocation(Request $request, $locationId) {
        $commentForm  = $this->createForm(CommentType::class, null, [
            'action' => $this->generateUrl('user_endLocation', [ 'locationId' => $this->encryptor->encrypt($locationId)]),
            'method' => 'POST'
        ])->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($location = $em->getRepository(Location::class)
                               ->find($locationId)
            ) {
                if ($content = $commentForm->get('comment')->getData()) {
                    $comment = new Comment();
                    $comment->setAnnounce($location->getAnnounce())
                            ->setUser($this->getUser())
                            ->setRate($request->request->get('rate'))
                            ->setContent($content);
                    $em->persist($comment);
                }
                $location->getAnnounce()->setEnable(true);
                $location->setReturned(true)
                         ->setReturnedAt(new \DateTime());
                $em->persist($location);
                $em->flush();
            }

            return $this->redirectToRoute('user_history');
        }

        $commentForm = $commentForm->createView();

        return compact('commentForm');
    }
}
