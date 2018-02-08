<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\User;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * SuperAdminRepository
 */
class SuperAdminRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param User $user
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
    }

    /**
     * @param User $user
     */
    public function remove(User $user)
    {
        $this->_em->remove($user);
        $this->_em->flush($user);
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b')
            ->leftJoin('a.subscription', 'b')
            ;

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
