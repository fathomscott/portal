<?php
namespace BackendBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use BackendBundle\Entity\User;

/**
 * Class ResetPasswordEvent
 */
class ResetPasswordEvent extends Event
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    final public function getUser()
    {
        return $this->user;
    }
}
