<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Subscription;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * SubscriptionRepository
 */
class SubscriptionRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param Subscription $subscription
     */
    public function save(Subscription $subscription)
    {
        $this->_em->persist($subscription);
        $this->_em->flush($subscription);
    }

    /**
     * @param Subscription $subscription
     */
    public function remove(Subscription $subscription)
    {
        $this->_em->remove($subscription);
        $this->_em->flush($subscription);
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
     * @param Agent $agent
     * @param null  $filters
     * @return QueryBuilder
     */
    public function getFindAllByAgentQueryBuilder(Agent $agent, $filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'b')
            ->where('a.user = :user')
            ->setParameter('user', $agent);

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindOverdueQueryBuilder($filters = null)
    {
	$now = date('Y-m-d');
        $qb = $this->createQueryBuilder('a')
            ->select('a, pl, us')
            ->join('a.plan', 'pl')
            ->join('a.user', 'us')
            ->where('a.status = :status')
            ->andWhere('a.dueDate <= :dueDate')
            ->setParameter(':status', Subscription::STATUS_ACTIVE)
            ->setParameter(':dueDate', $now)
            ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param integer   $limit
     * @return Subscription[]
     */
    public function getFindRenewableSubscriptions($startDate, $endDate, $limit = 2000)
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.plan', 'pl')
            ->join('a.user', 'us')
            ->where('a.dueDate >= :startDate')
            ->andWhere('a.dueDate <= :endDate')
	    ->andWhere('us.accountType NOT LIKE :accountType')
            ->setParameter(':startDate', $startDate)
            ->setParameter(':endDate', $endDate)
	    ->setParameter(':accountType','%ADMIN')
            ->setMaxResults($limit)
            ->orderBy('a.lastAttempt', 'asc');
        return $qb->getQuery()->execute();
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
                ->innerJoin('BackendBundle:Agent', 'd', Join::WITH, 'd.id = us.id')
                ->innerJoin('d.agentDistricts', 'e')
                ->leftJoin('e.district', 'f')
                ->andWhere('f.id IN (:district_ids)')
                ->setParameter('district_ids', $filters['district']);
        }

        if (isset($filters['state']) && !empty($filters['state'])) {
            $queryBuilder
                ->innerJoin('BackendBundle:Agent', 'd', Join::WITH, 'd.id = us.id')
                ->innerJoin('d.agentDistricts', 'e')
                ->leftJoin('e.district', 'f')
                ->leftJoin('f.state', 'g')
                ->andWhere('g.id = :state_id')
                ->setParameter('state_id', $filters['state']);
        }

        return $queryBuilder;
    }
}
