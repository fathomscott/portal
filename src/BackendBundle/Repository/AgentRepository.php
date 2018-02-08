<?
namespace BackendBundle\Repository;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Subscription;
use BackendBundle\Entity\User;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * AgentRepository
 */
class AgentRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param User $user
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param User $user
     */
    public function remove(User $user)
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

    /**
     * @param null $filters
     * @return Agent[]
     */
    public function getFindAllQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
	    ->select('a')
            ->leftJoin('a.state', 'b')
            ->orderBy('a.lastName','ASC')
	    ->addOrderBy('a.firstName','ASC')
            //->select('a, b')
            //->leftJoin('a.subscription', 'b')
            ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @return Agent[]
     */
    public function getAgentsWithExpiringDocuments()
    {
        return $this->createQueryBuilder('a')
            ->select('a, b, c')
            ->leftJoin('a.documents', 'b', Join::WITH, '(b.expirationDate = :thirty OR b.expirationDate = :twenty OR b.expirationDate = :ten OR b.expirationDate = :tomorrow)')
            ->leftJoin('b.documentOption', 'c')
            ->where('b.expirationDate >= :tomorrow')
            ->andWhere('b.expirationDate <= :thirty')
            ->setParameters([
                'thirty' => (new \DateTime('+30 days'))->format('Y-m-d'),
                'twenty' => (new \DateTime('+20 days'))->format('Y-m-d'),
                'ten' => (new \DateTime('+10 days'))->format('Y-m-d'),
                'tomorrow' => (new \DateTime('tomorrow'))->format('Y-m-d'),
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array|Agent[]
     */
    public function getAgentsWithMLSDuesRequiredDistricts()
    {
        return $this->createQueryBuilder('a')
            ->join('a.agentDistricts', 'b')
            ->join('b.district', 'c')
            ->join('a.subscription', 'd')
            ->where('c.MLSDuesRequired = true')
            ->andWhere('d.status != :deboarded_status')
            ->setParameter('deboarded_status', Subscription::STATUS_DEBOARDED)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     */
    public function getReferredActiveAgents(User $user)
    {
        $this->createQueryBuilder('a')
            ->select('a, b, c, d, e')
            ->join('a.referringReferrals', 'b')
            ->join('b.referredUser', 'c')
            ->join('c.subscription', 'd')
            ->join('d.plan', 'e')
            ->where('c.id = :referringUser')
            ->andWhere('d.status <> :deboarded_status')
            ->setParameters([
                'deboarded_status' => Subscription::STATUS_DEBOARDED,
                'referringUser' => $user->getId(),
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $filters
     * @return QueryBuilder
     */
    public function applyFilters(QueryBuilder $queryBuilder, $filters)
    {
	if (isset($filters['name']) && !empty($filters['name'])) {
            $queryBuilder
                ->andWhere("CONCAT(a.firstName,' ',a.nickName,' ',a.middleName,' ',a.lastName) like :name")
                ->setParameter('name', '%'.$filters['name'].'%');
	}

	if (isset($filters['email']) && !empty($filters['email'])) {
            $queryBuilder
                ->andWhere('a.email = :email OR a.personalEmail = :email')
                ->setParameter('email', $filters['email']);
	}

        if (isset($filters['district']) && !empty($filters['district'])) {
            $queryBuilder
                ->leftJoin('a.agentDistricts', 'c')
                ->leftJoin('c.district', 'd')
                ->andWhere('d.id IN (:district_ids)')
                ->setParameter('district_ids', $filters['district']);
        }

        if (isset($filters['state']) && !empty($filters['state'])) {
            $queryBuilder
                ->leftJoin('a.agentDistricts', 'c')
                ->leftJoin('c.district', 'd')
                ->leftJoin('d.state', 'e')
                ->andWhere('e.id = :state_id')
                ->setParameter('state_id', $filters['state']);
        }

	if (!isset($filters['status'])) {
		$filters['status'] = 1;
	}
        if (isset($filters['status']) && (!empty($filters['status']) or ($filters['status'] == 0))) {
            $queryBuilder
                ->andWhere('a.status = :status')
                ->setParameter('status', $filters['status']);
        }

        $filterBehavior = new Behaviors\FilterBehavior();

        return $filterBehavior
            ->setLikeColumns(['firstName', 'lastName', 'personalEmail'])
            ->applyFilters($queryBuilder, $filters)
            ;
    }
}
