<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Referral;
use BackendBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * ReferralRepository
 */
class ReferralRepository extends EntityRepository
{
    /**
     * @param Referral $referral
     */
    public function save(Referral $referral)
    {
        $this->_em->persist($referral);
        $this->_em->flush($referral);
    }

    /**
     * @param Referral $referral
     */
    public function remove(Referral $referral)
    {
        $this->_em->remove($referral);
        $this->_em->flush($referral);
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
     * @param User $user
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindByUserQueryBuilder(User $user, $filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.referredUser', 'b')
            ->join('a.referringUser', 'c')
            ->where('a.referringUser = :user')
            ->setParameter('user', $user)
        ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param null|array $filters
     * @return QueryBuilder
     */
    public function countReferralsQueryBuilder($filters = null)
    {

	$qb = $this->createQueryBuilder('a')
		->select(array('count(a.id) as referralCount', 'b.id', 'b.lastName', 'b.firstName', 'b.email', 'c.joinedDate'))
		->join('a.referringUser', 'b')
		->innerJoin('BackendBundle:Agent', 'c', Join::WITH, 'c.id = b.id')
		->groupBy('a.referringUser')
		->orderBy('referralCount', 'desc');
/*
        $qb = $this->createQueryBuilder('a')
            ->select(array('count(a.id) as referralCount', 'b.id', 'b.lastName', 'b.firstName', 'b.email', 'b.joinedDate'))
            ->join('a.referringUser', 'b')
            ->groupBy('a.referringUser')
            ->orderBy('referralCount', 'desc');
*/
        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @return integer
     */
    public function countAllReferringUsers()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(DISTINCT a.referringUser)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array|null   $filters
     * @param array|null   $rootAlias
     * @return QueryBuilder
     */
    public function applyFilters(QueryBuilder $queryBuilder, $filters, array $rootAlias = null)
    {
        $aliases = $rootAlias !== null ? $rootAlias : $queryBuilder->getRootAliases();

        $filterBehavior = new Behaviors\FilterBehavior();

        if (isset($filters['fromDateAdded']) && !empty($filters['fromDateAdded'])) {
            $queryBuilder
                ->andWhere(sprintf('%s.created >= :fromDateAdded', $aliases[0]))
                ->setParameter('fromDateAdded', \DateTime::createFromFormat('m/d/Y H:i:s', $filters['fromDateAdded'].' 00:00:00'))
            ;
        }
        if (isset($filters['toDateAdded']) && !empty($filters['toDateAdded'])) {
            $queryBuilder
                ->andWhere(sprintf('%s.created <= :toDateAdded', $aliases[0]))
                ->setParameter('toDateAdded', \DateTime::createFromFormat('m/d/Y H:i:s', $filters['toDateAdded'].' 23:59:59'))
            ;
        }

        if (isset($filters['district']) && !empty($filters['district'])) {
            $queryBuilder
                ->innerJoin('BackendBundle:Agent', 'c', Join::WITH, 'c.id = b.id')
                ->innerJoin('c.agentDistricts', 'd')
                ->leftJoin('d.district', 'e')
                ->andWhere('e.id IN (:district_ids)')
                ->setParameter('district_ids', $filters['district']);
        }

        if (isset($filters['state']) && !empty($filters['state'])) {
            $queryBuilder
                ->innerJoin('BackendBundle:Agent', 'c', Join::WITH, 'c.id = b.id')
                ->innerJoin('c.agentDistricts', 'd')
                ->leftJoin('d.district', 'e')
                ->leftJoin('e.state', 'f')
                ->andWhere('f.id = :state_id')
                ->setParameter('state_id', $filters['state']);
        }

        return $filterBehavior
            ->setLikeColumns([])
            ->applyFilters($queryBuilder, $filters)
        ;
    }
}
