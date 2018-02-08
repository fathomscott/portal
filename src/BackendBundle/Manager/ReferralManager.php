<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Referral;
use BackendBundle\Entity\User;
use BackendBundle\Repository\ReferralRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class ReferralManager
 */
class ReferralManager
{
    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param ReferralRepository $referralRepository
     * @param Paginator          $paginator
     */
    public function __construct(
        ReferralRepository $referralRepository,
        Paginator $paginator
    ) {
        $this->referralRepository = $referralRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|Referral
     */
    public function find($id)
    {
        return $this->referralRepository->find($id);
    }

    /**
     * @return Referral[]
     */
    public function getFindAll()
    {
        return $this->referralRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|Referral
     */
    public function findOneBy($param)
    {
        return $this->referralRepository->findOneBy($param);
    }

    /**
     * @param Referral $referral
     * @return Referral
     */
    public function save(Referral $referral)
    {
        $this->referralRepository->save($referral);

        return $referral;
    }

    /**
     * @param Referral $referral
     */
    public function remove(Referral $referral)
    {
        $this->referralRepository->remove($referral);
    }


    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->referralRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param User    $user
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindByUserPaginator(User $user, $page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->referralRepository->getFindByUserQueryBuilder($user, $filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function countReferralsPaginator($page, $filters = null, $limit = 50)
    {
        $queryBuilder = $this->referralRepository->countReferralsQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, array(
            'distinct' => false,
        ));
    }

    /**
     * @param User $user
     * @return array|User[]
     */
    public function getReferralList(User $user)
    {
        /** @var Referral[] $referrals */
        $referrals = $this
            ->referralRepository
            ->getFindByUserQueryBuilder($user)
            ->getQuery()->execute();

        $users = [];
        foreach ($referrals as $referral) {
            $users[] = $referral->getReferredUser();
        }

        return $users;
    }

    /**
     * @return integer
     */
    public function countReferringUsers()
    {
        return $this->referralRepository->countAllReferringUsers();
    }
}
