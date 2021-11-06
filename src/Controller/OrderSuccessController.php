<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }
    /**
     * @Route("/commande/success/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['stripeSessionId'=>$stripeSessionId]);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if (!$order->getIspaid()) {

            // Vider la session cart
            $cart->remove();
            // Modifier le status isPaid à 1 (true)
            $order->setIsPaid(1);
            $this->em->flush();

            // Envoyer un email à notre client pour lui confirmer sa commande

        }
        // Afficher les quelques informations de la commande de l'utilisateur

        return $this->render('order_success/index.html.twig',[
            'order'=>$order
        ]);
    }
}
