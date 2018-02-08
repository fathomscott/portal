<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\CreditCard;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * CreditCardRepository
 */
class CreditCardRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param CreditCard $creditCard
     */
    public function save(CreditCard $creditCard)
    {
        $this->_em->persist($creditCard);
        $this->_em->flush($creditCard);
    }

    /**
     * @param CreditCard $creditCard
     */
    public function remove(CreditCard $creditCard)
    {
        $this->_em->remove($creditCard);
        $this->_em->flush($creditCard);
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
