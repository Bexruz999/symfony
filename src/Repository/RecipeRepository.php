<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function paginateRecipes(int $page, ?int $userId): PaginationInterface
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
                'sortFieldAllowlist' => ['r.id', 'r.title']
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

    public function findWithDurationLowerThan(int $duration)
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'c')
            ->where('r.duration < :duration')
            ->orderBy('r.duration', 'ASC')
            ->leftJoin('r.category', 'c')
            ->setMaxResults(10)
            ->setParameter('duration', $duration)
            ->getQuery()
            ->getResult();
    }

    public function fintTotalDuration()
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.duration) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
