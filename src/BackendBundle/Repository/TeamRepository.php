<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Team;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * TeamRepository
 */
class TeamRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param Team $team
     */
    public function save(Team $team)
    {
        $this->_em->persist($team);
        $this->_em->flush($team);
    }

    /**
     * @param Team $team
     */
    public function remove(Team $team)
    {
        $this->_em->remove($team);
        $this->_em->flush($team);
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllByQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b, c')
            ->leftJoin('a.members', 'b')
            ->leftJoin('a.district', 'c')
            ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param Agent $agent
     * @param null  $filters
     * @return QueryBuilder
     */
    public function getFindAllByAgentQueryBuilder(Agent $agent, $filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b, c')
            ->leftJoin('a.members', 'b')
            ->leftJoin('a.district', 'c')
            ->where('b.agent = :agent')
            ->andWhere('b.token IS NULL') // only accepted teams
            ->setParameter('agent', $agent);

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
        if (isset($filters['district']) && !empty($filters['district'])) {
            $queryBuilder
                ->where('c.id IN (:district_ids)')
                ->setParameter('district_ids', $filters['district']);
        }

        if (isset($filters['state']) && !empty($filters['state'])) {
            $queryBuilder
                ->leftJoin('c.state', 'd')
                ->where('d.id = :state_id')
                ->setParameter('state_id', $filters['state']);
        }

        return $queryBuilder;
    }
}
