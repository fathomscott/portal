<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\State;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * StateRepository
 */
class StateRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param State $state
     */
    public function save(State $state)
    {
        $this->_em->persist($state);
        $this->_em->flush($state);
    }

    /**
     * @param State $state
     */
    public function remove(State $state)
    {
        $this->_em->remove($state);
        $this->_em->flush($state);
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
		   ->orderBy('a.name', 'ASC');

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @return array
     */
    public function getAllForAgentImporter()
    {
        return $this->createQueryBuilder('a')
            ->select('a, b')
            ->leftJoin('a.districts', 'b')
            ->getQuery()
            ->getArrayResult();
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
