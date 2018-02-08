<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\District;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * DistrictRepository
 */
class DistrictRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param District $district
     */
    public function save(District $district)
    {
        $this->_em->persist($district);
        $this->_em->flush($district);
    }

    /**
     * @param District $district
     */
    public function remove(District $district)
    {
        $this->_em->remove($district);
        $this->_em->flush($district);
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b')
            ->leftJoin('a.state', 'b')
	    ->orderBy('a.name', 'ASC')
            ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param Agent $agent
     * @return QueryBuilder
     */
    public function getAllForAgentFormQueryBuilder(Agent $agent)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b, c')
            ->leftJoin('a.state', 'b')
            ->orderBy('a.name', 'ASC')
        ;

        /* add AgentDistrict to check if district already have district director */
        if (null !== $agent->getId()) {
            $qb->leftJoin('a.agentDistricts', 'c', Join::WITH, 'c.districtDirector = true AND c.agent != :agent')
                ->setParameter('agent', $agent);
        } else {
            $qb->leftJoin('a.agentDistricts', 'c', Join::WITH, 'c.districtDirector = true');
        }

        return $qb;
    }

    /**
     * @param Agent $agent
     * @return QueryBuilder
     */
    public function getByAgentQueryBuilder(Agent $agent)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.agentDistricts', 'b')
            ->where('b.agent = :agent')
            ->setParameter('agent', $agent);
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
