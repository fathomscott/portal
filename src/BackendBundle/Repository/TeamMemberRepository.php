<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Team;
use BackendBundle\Entity\TeamMember;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * TeamMemberRepository
 */
class TeamMemberRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param TeamMember $teamMember
     */
    public function save(TeamMember $teamMember)
    {
        $this->_em->persist($teamMember);
        $this->_em->flush($teamMember);
    }

    /**
     * @param TeamMember $teamMember
     */
    public function remove(TeamMember $teamMember)
    {
        $this->_em->remove($teamMember);
        $this->_em->flush($teamMember);
    }

    /**
     * @param Team $team
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllByTeamQueryBuilder(Team $team, $filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.agent', 'b')
            ->where('a.team = :team')
            ->setParameter('team', $team)
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
