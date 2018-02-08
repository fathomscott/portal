<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\BillingOption;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * BillingOptionRepository
 */
class BillingOptionRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param BillingOption $billingTYpe
     */
    public function save(BillingOption $billingTYpe)
    {
        $this->_em->persist($billingTYpe);
        $this->_em->flush();
    }

    /**
     * @param BillingOption $billingTYpe
     */
    public function remove(BillingOption $billingTYpe)
    {
        $this->_em->remove($billingTYpe);
        $this->_em->flush();
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
