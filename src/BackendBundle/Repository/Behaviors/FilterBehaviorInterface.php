<?php
namespace BackendBundle\Repository\Behaviors;

use Doctrine\ORM\QueryBuilder;

/**
 * Interface FilterBehaviorInterface
 * @package BackendBundle\Repository\Behaviors
 */
interface FilterBehaviorInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $filters
     * @return mixed
     */
    public function applyFilters(QueryBuilder $queryBuilder, $filters);
}
