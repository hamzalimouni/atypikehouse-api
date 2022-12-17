<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/api/stripe/public', name: 'app_stripe_key_public')]
    public function public(): JsonResponse
    {
        return new JsonResponse(['public_key' => $_ENV['STRIPE_PUBLIC_KEY']]);
    }
    #[Route('/api/stripe/client_secret', name: 'app_stripe_key_client')]
    public function private(Request $request): JsonResponse
    {
        $amount = json_decode($request->getContent(), true)['amount'];
        $stripe = new \Stripe\StripeClient($_ENV['STRIPE_PRIVATE_KEY']);
        return new JsonResponse($stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => 'eur',
            // 'automatic_payment_methods' => [
            //     'enabled' => true,
            // ],
            'payment_method_types' => ['card'],
        ]));
    }
}
