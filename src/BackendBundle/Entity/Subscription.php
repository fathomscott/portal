<?php
namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 */
class Subscription
{
    const STATUS_ACTIVE    = 3;
    const STATUS_LATE      = 2;
    const STATUS_DEBOARDED = 1;

    public static $statuses = array(
        'labels.active'    => self::STATUS_ACTIVE,
        'labels.late'      => self::STATUS_LATE,
        'labels.deboarded' => self::STATUS_DEBOARDED,
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dueDate;

    /**
     * @var \BackendBundle\Entity\User
     */
    private $user;

    /**
     * @var \BackendBundle\Entity\Plan
     */
    private $plan;

    /**
     * @var \DateTime
     */
    private $lastAttempt;

    /**
     * @var integer
     */
    private $status = self::STATUS_ACTIVE;

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
     * @return \DateTime
     */
    public function getLastAttempt()
    {
        return $this->lastAttempt;
    }

    /**
     * @param \DateTime $lastAttempt
     */
    public function setLastAttempt($lastAttempt)
    {
        $this->lastAttempt = $lastAttempt;
    }

    /**
     * @return Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @param Plan $plan
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @param \DateTime $dueDate
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getStatusName()
    {
        if (in_array($this->status, self::$statuses, true)) {
            return array_search($this->status, self::$statuses);
        }

        return null;
    }
}
