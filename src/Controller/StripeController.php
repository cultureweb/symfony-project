<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $em,Cart $cart, $reference): Response
    {
        Stripe::setApiKey('sk_test_51JpH8BHD4Rm2YyTMkdiooBbbdM01prU8c9ARm5GyCPEFK479tRyW0tE9NusCKyFLKRXe272cdstAPDfGarMm7bnv00GV6PAEKt');

        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://localhost:8741';

        $order = $em->getRepository(Order::class)->findOneBy(array('reference'=>$reference));

        if(!$order) {
            new JsonResponse(['error'=>'order']);
        }

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $em->getRepository(Product::class)->findOneBy(array('name'=>$product->getProduct()));

            $products_for_stripe[] = [
                'price_data'=> [
                    'currency'=>'eur',
                    'unit_amount'=>$product->getPrice(),
                    'product_data'=>[
                        'name' => $product->getProduct(),
                        'images'=>[$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }
        $products_for_stripe[] = [
            'price_data'=> [
                'currency'=>'eur',
                'unit_amount'=>$order->getCarrierPrice() * 100,
                'product_data'=>[
                    'name' => $order->getCarrierName(),
                    'images'=>[$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        $checkout_session = Session::create([
            'customer_email'=>$this->getUser()->getEmail(),
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
