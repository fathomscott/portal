<?php
namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * SystemMessage
 */
class SystemMessage
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var User[]
     */
    private $users;

    /**
     * SystemMessage constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->message;
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
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users->add($user);
    }
}
