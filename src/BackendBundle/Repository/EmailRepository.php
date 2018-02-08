<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Email;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * EmailRepository
 */
class EmailRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param Email $email
     */
    public function save(Email $email)
    {
        $this->_em->persist($email);
        $this->_em->flush($email);
    }

    /**
     * @param Email $email
     */
    public function remove(Email $email)
    {
        $this->_em->remove($email);
        $this->_em->flush($email);
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
