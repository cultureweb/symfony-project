<?php

namespace App\Repository;

use App\Entity\Product;
use App\Classes\Search;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * ProductRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Returns an array of Product objects
     * @return Product[]
     */

    public function findWithSearch(Search $search):array
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c');

        if(!empty($search->categories)){
            $query = $query
                ->andWhere( 'c.id IN (:categories)')
                ->setParameter('categories',$search->categories);
        }
        if(!empty($search->string)){

            $query = $query
                ->andWhere('p.name LIKE :string')
                ->setParameter('string', "%{$search->string}%"); // "%{ }%" permet d'indiquer à Symfony que l'on veut effectuer une requête partielle.
        }

        return $query->getQuery()->getArrayResult();
    }
}
