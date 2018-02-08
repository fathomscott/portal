<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Referral;
use BackendBundle\Entity\Subscription;
use BackendBundle\Repository\SubscriptionRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;

/**
 * Class SubscriptionManager
 */
class SubscriptionManager
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param SubscriptionRepository $subscriptionRepository
     * @param Paginator              $paginator
     */
    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        Paginator $paginator
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|Subscription
     */
    public function find($id)
    {
        return $this->subscriptionRepository->find($id);
    }

    /**
     * @return Subscription[]
     */
    public function getFindAll()
    {
        return $this->subscriptionRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|Subscription
     */
    public function findOneBy($param)
    {
        return $this->subscriptionRepository->findOneBy($param);
    }

    /**
     * @param Subscription $subscription
     * @return Subscription
     */
    public function save(Subscription $subscription)
    {
        $this->subscriptionRepository->save($subscription);

        return $subscription;
    }

    /**
     * @param Subscription $subscription
     */
    public function remove(Subscription $subscription)
    {
        $this->subscriptionRepository->remove($subscription);
    }

    /**
     * @param Agent   $agent
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllByAgentPaginator(Agent $agent, $page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->subscriptionRepository->getFindAllByAgentQueryBuilder($agent, $filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param integer $page
     * @param null    $filters
     * @param int     $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return PaginationInterface
     */
    public function getFindOverduePaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.dueDate", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->subscriptionRepository->getFindOverdueQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, array(
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ));
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return Subscription[]
     */
    public function findRenewableSubscriptions(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->subscriptionRepository->getFindRenewableSubscriptions($startDate, $endDate);
    }

    /**
     * @param Subscription $subscription
     * @return float|int
     */
    public function countReferralDiscount(Subscription $subscription)
    {
        $referralDiscount = 0;
        $referredReferrals = $subscription->getUser()->getReferringReferrals();
        foreach ($referredReferrals as $referredReferral) { /** @var $referredReferral Referral */
            $user = $referredReferral->getReferringUser();
            if (Subscription::STATUS_DEBOARDED !== $user->getSubscription()->getStatus()) {
                $referralDiscount += $user->getSubscription()->getPlan()->getReferralDiscount();
            }
        }

        return $referralDiscount;
    }
}
