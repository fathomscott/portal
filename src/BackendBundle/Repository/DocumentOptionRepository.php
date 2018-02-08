<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\DocumentOption;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * DocumentOptionRepository
 */
class DocumentOptionRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param DocumentOption $documentOption
     */
    public function save(DocumentOption $documentOption)
    {
        $this->_em->persist($documentOption);
        $this->_em->flush($documentOption);
    }

    /**
     * @param DocumentOption $documentOption
     */
    public function remove(DocumentOption $documentOption)
    {
        $this->_em->remove($documentOption);
        $this->_em->flush($documentOption);
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
     * @return array
     */
    public function getAllForAgentImporter()
    {
        return $this->createQueryBuilder('a');
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
