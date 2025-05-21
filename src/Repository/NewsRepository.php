<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function save(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get latest news
     */
    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.insertDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get latest news for a specific category
     */
    public function findByCategoryPaginated(int $categoryId, int $page = 1, int $limit = 10): Paginator
    {
        $query = $this->createQueryBuilder('n')
            ->join('n.categories', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('n.insertDate', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery();

        return new Paginator($query);
    }

    /**
     * Get top viewed news
     */
    public function findTopViewedNews(int $limit = 10): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.views', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findPaginated(int $page = 1, int $limit = 10, string $sortBy = 'insertDate', string $order = 'DESC'): Paginator
    {
        $validSortFields = ['title', 'insertDate', 'views'];
        $validOrders = ['ASC', 'DESC'];

        // Validate sort field and order
        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'insertDate';
        }
        if (!in_array(strtoupper($order), $validOrders)) {
            $order = 'DESC';
        }

        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('n')
            ->leftJoin('n.categories', 'c')
            ->addSelect('c')
            ->orderBy('n.' . $sortBy, strtoupper($order))
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($queryBuilder->getQuery(), true);
    }

    /**
     * Search news with pagination
     */
    public function searchPaginated(string $query, int $page = 1, int $limit = 10, string $sortBy = 'insertDate', string $order = 'DESC'): Paginator
    {
        $validSortFields = ['title', 'insertDate', 'views'];
        $validOrders = ['ASC', 'DESC'];

        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'insertDate';
        }
        if (!in_array(strtoupper($order), $validOrders)) {
            $order = 'DESC';
        }

        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('n')
            ->leftJoin('n.categories', 'c')
            ->addSelect('c')
            ->where('n.title LIKE :query')
            ->orWhere('n.shortDescription LIKE :query')
            ->orWhere('n.content LIKE :query')
            ->orWhere('c.title LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('n.' . $sortBy, strtoupper($order))
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($queryBuilder->getQuery(), true);
    }
}
