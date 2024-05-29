<?php

namespace App\Repository;

use App\Entity\UserCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<UserCollection>
 */
class UserCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, UserCollection::class);
    }

    public function paginateCollections(int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('r')->leftJoin('r.category', 'c')->select('r', 'c');

        if ($userId) {
            $builder = $builder->andWhere('r.user = :user')
                ->setParameter(':user', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page,
            20,
            [
                'distinct' => false,
                'sortFieldAllowlist' => ['r.id', 'r.name']
            ]
        );


        /*return new Paginator(
            $this->createQueryBuilder('r')
                ->setFirstResult(($page -1) * $limit)
                ->setMaxResults($limit)
                ->getQuery()
                ->setHint(Paginator::HINT_ENABLE_DISTINCT, false), false
        );*/
    }

    public function findAllWithCount(): array
    {
        return $this->createQueryBuilder('cl')
            ->select('NEW App\\DTO\\UserCollectionWithCountDTO(cl.id, cl.name, Count(i.id))')
            ->leftJoin('cl.items', 'i')
            ->groupBy('cl.id')
            ->getQuery()
            ->getResult();
    }

    public function findWithItems($id)
    {
        return $this->createQueryBuilder('cl')
            ->select('cl', 'i')
            ->Where('cl.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('cl.items', 'i')
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return UserCollection[] Returns an array of UserCollection objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserCollection
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
