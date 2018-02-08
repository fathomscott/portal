<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\AgentNote;
use BackendBundle\Entity\User;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * AgentNoteRepository
 */
class AgentNoteRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param AgentNote $agentNote
     */
    public function save(AgentNote $agentNote)
    {
        $this->_em->persist($agentNote);
        $this->_em->flush($agentNote);
    }

    /**
     * @param AgentNote $agentNote
     */
    public function remove(AgentNote $agentNote)
    {
        $this->_em->remove($agentNote);
        $this->_em->flush($agentNote);
    }

    /**
     * @param Agent $agent
     * @param User  $user
     * @param null  $filters
     * @return QueryBuilder
     */
    public function getFindAllByAgentPaginator(Agent $agent, User $user, $filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.agent', 'b')
            ->where('a.agent = :agent')
            ->andWhere('(a.public = true OR a.author = :author)')
            ->setParameters([
                'agent' => $agent,
                'author' => $user,
            ])
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
