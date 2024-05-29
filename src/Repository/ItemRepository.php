<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Item::class);
    }

    public function paginateItems(int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('r')
            ->leftJoin('r.UserCollection', 'c')
            ->select('r', 'c');

        if ($userId) {
            $builder = $builder->andWhere('r.user = :user')
                ->setParameter(':user', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page,
            20,
            ['distinct' => false, 'sortFieldAllowlist' => ['r.id', 'r.name']]
        );

        /*return new Paginator(
            $this->createQueryBuilder('r')
                ->setFirstResult(($page -1) * $limit)
                ->setMaxResults($limit)
                ->getQuery()
                ->setHint(Paginator::HINT_ENABLE_DISTINCT, false), false
        );*/
    }

    public function paginateCollectionItems(int $page, ?int $collectionId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('r')
            ->leftJoin('r.UserCollection', 'c')
            ->select('r', 'c')
            ->andWhere('r.UserCollection = :cl')
            ->setParameter(':cl', $collectionId);

        return $this->paginator->paginate(
            $builder,
            $page,
            20,
            ['distinct' => false, 'sortFieldAllowlist' => ['r.id', 'r.name']]
        );
    }

    //    /**
    //     * @return Item[] Returns an array of Item objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Item
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
