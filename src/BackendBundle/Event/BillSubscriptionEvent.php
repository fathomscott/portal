<?php
namespace BackendBundle\Event;

use BackendBundle\Entity\Subscription;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BillSubscriptionEvent
 */
class BillSubscriptionEvent extends Event
{
    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * @var integer
     */
    private $amount;

    /**
     * @var integer
     */
    private $referralDiscount;

    /**
     * BillSubscriptionEvent constructor.
     * @param Subscription $subscription
     * @param integer      $amount
     * @param integer      $referralDiscount
     */
    public function __construct(Subscription $subscription, $amount, $referralDiscount = 0)
    {
        $this->subscription = $subscription;
        $this->amount = $amount;
        $this->referralDiscount = $referralDiscount;
    }

    /**
     * @return Subscription
     */
    final public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return integer
     */
    final public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return integer
     */
    final public function getReferralDiscount()
    {
        return $this->referralDiscount;
    }
}
