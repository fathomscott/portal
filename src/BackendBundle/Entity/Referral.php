<?php

namespace BackendBundle\Entity;

use BackendBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Referral
 */
class Referral
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var User
     */
    private $referredUser;

    /**
     * @var User
     */
    private $referringUser;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getReferredUser()
    {
        return $this->referredUser;
    }

    /**
     * @param User $referredUser
     */
    public function setReferredUser($referredUser)
    {
        $this->referredUser = $referredUser;
    }

    /**
     * @return User
     */
    public function getReferringUser()
    {
        return $this->referringUser;
    }

    /**
     * @param User $referringUser
     */
    public function setReferringUser($referringUser)
    {
        $this->referringUser = $referringUser;
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
}
