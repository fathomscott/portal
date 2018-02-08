<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\District;
use BackendBundle\Entity\Transaction;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * TransactionRepository
 */
class TransactionRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param Transaction $transaction
     */
    public function save(Transaction $transaction)
    {
        $this->_em->persist($transaction);
        $this->_em->flush($transaction);
    }

    /**
     * @param Transaction $transaction
     */
    public function remove(Transaction $transaction)
    {
        $this->_em->remove($transaction);
        $this->_em->flush($transaction);
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b, c')
            ->leftJoin('a.user', 'b')
            ->leftJoin('b.subscription', 'c')
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
    public function getFindVariableMlsDuesQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'b')
            ->join('a.district', 'c')
            ->where('c.MLSDuesRequired = true')
            ->andWhere('c.MLSDuesType = :type')
            ->andWhere('a.status = :pending_status')
            ->setParameters([
                'type' => District::MLS_DUES_TYPE_VARIABLE,
                'pending_status' => Transaction::STATUS_PENDING,
            ])
            ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }


    /**
     * @param Agent|null $agent
     * @return array|Transaction
     */
    public function getFixedMLSDuesPendingTransactions(Agent $agent = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.user', 'b')
            ->join('a.district', 'c')
            ->where('a.status = :pending_status')
            ->andWhere('c.MLSDuesRequired = true')
            ->andWhere('c.MLSDuesType = :type');

        if (null === $agent) {
            $qb->setParameters([
                'type' => District::MLS_DUES_TYPE_FIXED,
                'pending_status' => Transaction::STATUS_PENDING,
            ]);
        } else {
            $qb->andWhere('b.id = :user')
                ->setParameters([
                    'type' => District::MLS_DUES_TYPE_FIXED,
                    'pending_status' => Transaction::STATUS_PENDING,
                    'user' => $agent->getId(),
                ]);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $filters
     * @return QueryBuilder
     */
    public function applyFilters(QueryBuilder $queryBuilder, $filters)
    {
//echo "filters: ".$filters['toCreated'].", ".$filters['toCreated'].", ".$filters['status'].", ".$filters['user'];
        $aliases = $queryBuilder->getRootAliases();

        if (isset($filters['fromCreated']) && !empty($filters['fromCreated'])) {
            $queryBuilder
                ->andWhere(sprintf('%s.created >= :fromCreated', $aliases[0]))
                ->setParameter('fromCreated', \DateTime::createFromFormat('m/d/Y H:i:s', $filters['fromCreated'].' 00:00:00'))
            ;
        }

        if (isset($filters['toCreated']) && !empty($filters['toCreated'])) {
            $queryBuilder
                ->andWhere(sprintf('%s.created <= :toCreated', $aliases[0]))
                ->setParameter('toCreated', \DateTime::createFromFormat('m/d/Y H:i:s', $filters['toCreated'].' 23:59:59'))
            ;
        }

        if (isset($filters['district']) && !empty($filters['district'])) {
            $queryBuilder
                ->innerJoin('BackendBundle:Agent', 'd', Join::WITH, 'd.id = b.id')
                ->innerJoin('d.agentDistricts', 'e')
                ->leftJoin('e.district', 'f')
                ->andWhere('f.id IN (:district_ids)')
                ->setParameter('district_ids', $filters['district']);
        }

        if (isset($filters['state']) && !empty($filters['state'])) {
            $queryBuilder
                ->innerJoin('BackendBundle:Agent', 'd', Join::WITH, 'd.id = b.id')
                ->innerJoin('d.agentDistricts', 'e')
                ->leftJoin('e.district', 'f')
                ->leftJoin('f.state', 'g')
                ->andWhere('g.id = :state_id')
                ->setParameter('state_id', $filters['state']);
        }

        $filterBehavior = new Behaviors\FilterBehavior();

        return $filterBehavior
            ->setEqualsColumns(array('user', 'status'))
            ->applyFilters($queryBuilder, $filters)
            ;
    }
}
