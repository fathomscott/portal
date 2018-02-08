<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\LicenseCategory;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * LicenseCategoryRepository
 */
class LicenseCategoryRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param LicenseCategory $licenseCategory
     */
    public function save(LicenseCategory $licenseCategory)
    {
        $this->_em->persist($licenseCategory);
        $this->_em->flush($licenseCategory);
    }

    /**
     * @param LicenseCategory $licenseCategory
     */
    public function remove(LicenseCategory $licenseCategory)
    {
        $this->_em->remove($licenseCategory);
        $this->_em->flush($licenseCategory);
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
