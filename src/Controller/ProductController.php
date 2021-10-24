<?php

namespace App\Controller;

use App\Entity\Product;

use App\Form\SearchType;
use App\Repository\ProductRepository;
use App\Classes\Search;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{


    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    /**
     * @Route("/nos-produits", name="products")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {



        $search = new Search();

        $form = $this->createForm(SearchType::class, $search);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $products = $this->em->getRepository(Product::class)->findWithSearch($search);

        }
        else {
            $products = $this->em->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig',[
            'products'=> $products,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="product")
     */
    public function show($slug): Response
    {
        $product = $this->em->getRepository(Product::class)->findOneBy(['slug' => $slug]);


        if (!$product){
            return $this->redirectToRoute('products');
        }


        return $this->render('product/show.html.twig',[
            'product'=>$product
        ]);
    }
}
