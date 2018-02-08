<?php
namespace BackendBundle\Repository\Behaviors;

use Doctrine\ORM\QueryBuilder;

/**
 * Class FilterBehavior
 * @package BackendBundle\Repository\Behaviors
 */
class FilterBehavior implements FilterBehaviorInterface
{
    private $likeColumns = array();
    private $equalsColumns = array();
    private $columnsReferences = array();

    /**
     * @param array $likeColumns
     * @return $this
     */
    public function setLikeColumns(array $likeColumns)
    {
        $this->likeColumns = $likeColumns;

        return $this;
    }

    /**
     * @param array $equalsColumns
     * @return $this
     */
    public function setEqualsColumns(array $equalsColumns)
    {
        $this->equalsColumns = $equalsColumns;

        return $this;
    }

    /**
     * @param array $references
     * @return $this
     */
    public function setColumnsReferences(array $references)
    {
        $this->columnsReferences = $references;

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function addEqualsColumn($column)
    {
        $this->equalsColumns[] = $column;

        return $this;
    }

    /**
     * @param string $column
     * @param string $alias
     * @return $this
     */
    public function addColumnReference($column, $alias)
    {
        $this->columnsReferences[$column] = $alias;

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $filters
     * @return QueryBuilder
     */
    public function applyFilters(QueryBuilder $queryBuilder, $filters)
    {
        $aliases = $queryBuilder->getRootAliases();

        foreach ($filters as $key => $value) {
            $alias = $aliases[0];
            if (is_string($value)) {
                $value = trim($value);
            }
            if ($referenceAlias = $this->getReferenceAlias($key)) {
                $alias = $referenceAlias;
            }
            if ($this->isLikeColumn($key) && $value) {
                $queryBuilder
                    ->andWhere(sprintf('%s.%s LIKE :%s', $alias, $key, $key))
                    ->setParameter($key, '%'.addcslashes($value, '_%').'%')
                ;
            }
            if ($this->isEqualsColumn($key) && ($value || (isset($value) && $value === 0))) {
                $queryBuilder
                    ->andWhere(sprintf('%s.%s = :%s', $alias, $key, $key))
                    ->setParameter($key, $value)
                ;
            }
        }

        return $queryBuilder;
    }

    /**
     * @param string $key
     * @return boolean
     */
    private function isLikeColumn($key)
    {
        return in_array($key, $this->likeColumns);
    }

    /**
     * @param string $key
     * @return boolean
     */
    private function isEqualsColumn($key)
    {
        return in_array($key, $this->equalsColumns);
    }

    /**
     * @param string $key
     * @return boolean
     */
    private function getReferenceAlias($key)
    {
        return isset($this->columnsReferences[$key]) ? $this->columnsReferences[$key] : false;
    }
}
