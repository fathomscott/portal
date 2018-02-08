<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\AgentDistrict;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * AgentDistrictRepository
 */
class AgentDistrictRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param AgentDistrict $agentDistrict
     */
    public function save(AgentDistrict $agentDistrict)
    {
        $this->_em->persist($agentDistrict);
        $this->_em->flush($agentDistrict);
    }

    /**
     * @param AgentDistrict $agentDistrict
     */
    public function remove(AgentDistrict $agentDistrict)
    {
        $this->_em->remove($agentDistrict);
        $this->_em->flush($agentDistrict);
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
     * @param array $districtIds
     * @param Agent $agent
     * @return AgentDistrict|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkIfDistrictsHaveDirector(array $districtIds, Agent $agent)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b')
            ->leftJoin('a.district', 'b')
            ->where('a.districtDirector = true')
            ->andWhere('b.id IN (:ids)')
            ->setParameter('ids', $districtIds)
            ->setMaxResults(1);
        ;

        if (null !== $agent->getId()) {
            $qb->andWhere('a.agent != :agent')
                ->setParameter('agent', $agent);
        }

        return $qb->getQuery()->getOneOrNullResult();
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
