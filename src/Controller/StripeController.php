<?php

namespace App\Controller;

use App\Classes\Cart;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart): Response
    {
        Stripe::setApiKey('sk_test_51JpH8BHD4Rm2YyTMkdiooBbbdM01prU8c9ARm5GyCPEFK479tRyW0tE9NusCKyFLKRXe272cdstAPDfGarMm7bnv00GV6PAEKt');

        $YOUR_DOMAIN = 'http://localhost:8741';
        $products_for_stripe = [];

        foreach ($cart->getFull() as $product) {
            $products_for_stripe[]=[
                'price_data' => [
                    'currency'=>'eur',
                    'unit_amount'=>$product['product']->getPrice(),
                    'product_data'=>[
                        'name' => $product['product']->getName(),
                        'images'=>[$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()],
                    ],
                ],
                'quantity' => $product['quantity'],
            ];
        }
        $checkout_session = Session::create([
            'payment_method_types' => [
                'card',
            ],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);
        $response = new JsonResponse(['id'=>$checkout_session->id]);
        return $response;
    }
}
