<?php


namespace App\Classes;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Cart
 * @package App\Classes
 */
class Cart
{
    private $session;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     *
     */
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
}