<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\User;
use App\Repository\LocationRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $kernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->kernel = $appKernel;
    }

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
     * @ParamDecryptor(params={"locationId"})
     * @param \Swift_Mailer $mailer
     * @param string $locationId
     * @return RedirectResponse|Response
     */
    public function successPayment(\Swift_Mailer $mailer, string $locationId)
    {
        $em           = $this->getDoctrine()->getManager();
        /** @var LocationRepository $repoLocation */
        $repoLocation = $em->getRepository(Location::class);

        /** @var Location $location */
        if ($location = $repoLocation->find($locationId)) {
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

            $this->generatePDF($location, 'facture');
            $this->generatePDF($location, 'contrat');

            $message->attach(Swift_Attachment::fromPath(
                $this->kernel->getProjectDir() . '/public/uploads/pdf/factures/facture_'. $location->getId() .'.pdf',
                'application/pdf'
            ));
            $message->attach(Swift_Attachment::fromPath(
                $this->kernel->getProjectDir() . '/public/uploads/pdf/contrats/contrat_'. $location->getId() .'.pdf',
                'application/pdf'
            ));

            $mailer->send($message);

            /** @var User $user */
            $user      = $location->getUser();

            $user->setLoyaltyPoints($this->getLoyaltyPoint($user, $location->getPricePaid()));
            $location->getAnnounce()->setEnable(false);
            $em->persist($location);
            $em->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('payment/index.html.twig');
    }

    /**
     * @param User $user
     * @param int $pricePaid
     * @return int|mixed
     */
    public function getLoyaltyPoint(User $user, int $pricePaid)
    {
        $loyaltyPoints = $user->getLoyaltyPoints();
        $loyaltyPoints += $pricePaid;

        return $loyaltyPoints;
    }

    /**
     * @param Location $location
     * @param string $type
     */
    public function generatePDF(Location $location, string $type)
    {
        $html       = $pdfFilepath = "";
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $pdf = new Dompdf($pdfOptions);

        if ($type == "facture") {
            $html = $this->renderView('payment/partials/_facture.html.twig', ['location' => $location]);
        } elseif ($type == "contrat") {
            $html = $this->renderView('payment/partials/_contrat.html.twig', ['location' => $location]);
        }

        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $output = $pdf->output();

        if ($type == "facture") {
            $publicDirectory = $this->kernel->getProjectDir() . '/public/uploads/pdf/factures';
            $pdfFilepath =  $publicDirectory . '/facture_'.$location->getId().'.pdf';
        } elseif ($type == "contrat") {
            $publicDirectory = $this->kernel->getProjectDir() . '/public/uploads/pdf/contrats';
            $pdfFilepath =  $publicDirectory . '/contrat_'.$location->getId().'.pdf';
        }

        file_put_contents($pdfFilepath, $output);
    }
}
