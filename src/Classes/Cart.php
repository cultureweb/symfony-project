<?php


namespace App\Classes;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Cart
 * @package App\Classes
 */
class Cart
{
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;


    /**
     * Cart constructor.
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     */
    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->session = $session;
        $this->em = $em;

    }

    public function getFull(){

        $cartComplete = [];
        if($this->get()){
            foreach ($this->get() as $id => $quantity) {
                $productObject = $this->em->getRepository(Product::class)->findOneBy(['id'=> $id]);
                if(!$productObject){
                    $this->cancel($id);
                    continue;
                }
                $cartComplete[] = [
                    'product'=> $productObject,
                    'quantity'=>$quantity
                ];
            }
        }
        return $cartComplete;
    }

    public function add($id)
    {
        $cart = $this->session->get('cart',[]);
        if (!empty($cart[$id])){
            $cart[$id]++;
        }
        else {
            $cart[$id] = 1;
        }


        $this->session->set('cart',$cart);
    }

    public function get() {
        return $this->session->get('cart');
    }

    public function remove() {
        return $this->session->remove('cart');
    }

    public function cancel($id) {
        $cart = $this->session->get('cart');

        unset($cart[$id]);

        return$this->session->set('cart',$cart);
    }
    public function decrease($id){
        $cart = $this->session->get('cart');
        if ($cart[$id] > 1){
            $cart[$id]--;
        }
        else {
            unset($cart[$id]);
        }

        return$this->session->set('cart',$cart);

    }
}