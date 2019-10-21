<?php

namespace App\Controller;

use App\Entity\Location;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/payment", name="payment")
     */
    public function index()
    {
        $stripe = new Stripe();
        $stripe::setApiKey('sk_test_jWWKdFyljvdnfJbjevs74kQH000tfdnAdA');

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'name' => 'T-shirt',
                'description' => 'Comfortable cotton t-shirt',
                'images' => ['https://example.com/t-shirt.png'],
                'amount' => 500,
                'currency' => 'eur',
                'quantity' => 1,
            ]],
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
        ]);

        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    /**
     * @Route("/payment/success/{locationId}", name="successPayment")
     * @param \Swift_Mailer $mailer
     * @param $locationId
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamDecryptor(params={"locationId"})
     */
    public function successPayment(\Swift_Mailer $mailer, $locationId)
    {
        $em = $this->getDoctrine()->getManager();

        if ($location = $em->getRepository(Location::class)
                           ->find($locationId)
        ) {
            $message = (new \Swift_Message('E-motion: Paiement effectuée'))
                ->setFrom('admin@e-motion.fr')
                ->setTo($location->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'payment/partials/_email.html.twig'
                    ),
                    'text/html'
                )
            ;

            $mailer->send($message);

            $location->getAnnounce()->setEnable(false);
            $em->persist($location);
            $em->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('payment/index.html.twig');
    }
}
