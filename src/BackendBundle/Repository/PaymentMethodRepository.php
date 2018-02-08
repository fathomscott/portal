<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\PaymentMethod;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * PaymentMethodRepository
 */
class PaymentMethodRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param PaymentMethod $paymentMethod
     */
    public function save(PaymentMethod $paymentMethod)
    {
        $this->_em->persist($paymentMethod);
        $this->_em->flush($paymentMethod);
    }

    /**
     * @param PaymentMethod $paymentMethod
     */
    public function remove(PaymentMethod $paymentMethod)
    {
        $this->_em->remove($paymentMethod);
        $this->_em->flush($paymentMethod);
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
