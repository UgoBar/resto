<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Service\StripeService;
use App\Service\UserCartService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    public function __construct(
        private StripeService $stripeService
    ) {
        $this->stripeService->init();
    }

    /**
     * @throws \Stripe\Exception\ApiErrorException
     * doc https://stripe.com/docs/api/checkout/sessions/create
     */
    #[Route('/stripe/payment', name: 'app_payment', methods: ['POST'])]
    public function askPayment(Request $request): Response
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        if(empty($data) || !$this->getUser()) {
            return new JsonResponse(['error' => true]);
        }

        $cart = $data['cart'];
        $lineItems = [];
        foreach($cart as $row) {
            $tempArray = [
                'quantity' => $row['quantity'],
                'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => $row['name'],
                        'images' => [$row['img']]
                    ],
                    'unit_amount' => $row['quantity'] * 100
                ]
            ];
            array_push($lineItems, $tempArray);
        }

        $stripeSession = Session::create(
            [
                'mode' => 'payment',
                'success_url' => $this->generateUrl('app_payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'line_items' => $lineItems,
                // Données qui seront récupérées telles quelles dans la réponse de Stripe
                'metadata' => ['app_no_sushis' => 'nonono']
            ]
        );

        dd($stripeSession);
        // TODO - envoyer au paiement et afficher une belle page de commande finie

        file_put_contents('../StripeSessions/askPayment.json', $stripeSession);
        return $this->redirect($stripeSession->url, 303);

//        return $this->render('home/index.html.twig', [
//            'controller_name' => 'HomeController',
//        ]);
    }

    #[Route('/stripe/payment-success', name: 'app_payment_success')]
    public function paymentSuccess(): Response
    {
        return new Response('Payment ok');
    }

    /**
     * WEBHOOK
     * doc https://dashboard.stripe.com/test/webhooks/create?endpoint_location=local
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Stripe\Exception\SignatureVerificationException
     */
    #[Route('/stripe/payment-webhook', name: 'app_payment_webhook')]
    public function webhook(Request $request, EntityManagerInterface $em, UserCartService $cartService): Response
    {
        $cliWebhookKey = 'whsec_cafd554893b0edb05bb69122359b992bcbb656741ebebe595b6dec793ac96524';
        $this->stripeService->init($cliWebhookKey);
        $signature = $request->headers->get('stripe-signature');
        $requestBody = $request->getContent();

        $event = Webhook::constructEvent(
            $requestBody, $signature, $cliWebhookKey
        );

        if($event->type === 'checkout.session.completed') {
            $cart = $cartService->getOrCreateCurrentCart($this->getUser());
            if(!$cart) {
                return new JsonResponse(['error' => true]);
            }

            $cart->setState(Cart::FINALISED);
            $em->persist($cart);
            $em->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['error' => true]);
    }
}
