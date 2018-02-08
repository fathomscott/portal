<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Plan;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * PlanRepository
 */
class PlanRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param Plan $plan
     */
    public function save(Plan $plan)
    {
        $this->_em->persist($plan);
        $this->_em->flush($plan);
    }

    /**
     * @param Plan $plan
     */
    public function remove(Plan $plan)
    {
        $this->_em->remove($plan);
        $this->_em->flush($plan);
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a');

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }
    
    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindActivePublicQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->andWhere('a.public = :public')
            ->setParameter('status', Plan::STATUS_ACTIVE)
            ->setParameter('public', true);

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $filters
     * @return QueryBuilder
     */
    public function applyFilters(QueryBuilder $queryBuilder, $filters)
    {
        /* TODO applying filters */

        return $queryBuilder;
    }
}
