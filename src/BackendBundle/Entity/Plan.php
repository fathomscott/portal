<?php
namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Plan
 */
class Plan
{
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 0;

    public static $statuses = array(
        'labels.active' => self::STATUS_ACTIVE,
        'labels.disabled' => self::STATUS_DISABLED,
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $public = true;

    /**
     * @var integer
     */
    private $status = self::STATUS_ACTIVE;

    /**
     * @var string
     * @Assert\Range(min = 0.5)
     */
    private $monthlyPrice;

    /**
     * @var string
     * @Assert\Range(min = 0.5)
     */
    private $annualPrice;

    /**
     * @var float
     * @Assert\Range(min = 0.5)
     */
    private $referralDiscount;


    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var ArrayCollection|Subscription[]
     */
    private $subscriptions;

    /**
     * Plan constructor.
     */
    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param boolean $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMonthlyPrice()
    {
        return $this->monthlyPrice;
    }

    /**
     * @param string $monthlyPrice
     */
    public function setMonthlyPrice($monthlyPrice)
    {
        $this->monthlyPrice = $monthlyPrice;
    }

    /**
     * @return string
     */
    public function getAnnualPrice()
    {
        return $this->annualPrice;
    }

    /**
     * @param float $annualPrice
     */
    public function setAnnualPrice($annualPrice)
    {
        $this->annualPrice = $annualPrice;
    }

    /**
     * @return float
     */
    public function getReferralDiscount()
    {
        return $this->referralDiscount;
    }

    /**
     * @param string $referralDiscount
     */
    public function setReferralDiscount($referralDiscount)
    {
        $this->referralDiscount = $referralDiscount;
    }

    /**
     * @return array
     */
    public function getStatusName()
    {
        if (in_array($this->status, self::$statuses, true)) {
            return array_search($this->status, self::$statuses);
        }

        return null;
    }

    /**
     * @return Subscription[]|ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param Subscription $subscription
     */
    public function addSubscription(Subscription $subscription)
    {
        $this->subscriptions->add($subscription);
    }

    /**
     * @param Subscription $subscription
     */
    public function removeSubscription(Subscription $subscription)
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
        }
    }
}
