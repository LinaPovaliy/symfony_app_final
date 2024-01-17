<?php

namespace App\Repository;

use App\Entity\Advertisement;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Advertisement>
 *
 * @method Advertisement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advertisement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advertisement[]    findAll()
 * @method Advertisement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertisementRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 2;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advertisement::class);
    }

    public function getAdvertisementPaginator(Category $category, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.category = :category')
            ->andWhere('a.status = :status')
            ->setParameter('category', $category)
            ->setParameter('status', 'published')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    public function getTotalByStatus($status)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.status = :status')
            ->setParameter('status', $status);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
