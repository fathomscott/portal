<?php
namespace BackendBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use BackendBundle\Entity\User;

/**
 * Class ForgotPasswordEvent
 */
class ForgotPasswordEvent extends Event
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    final public function getUser()
    {
        return $this->user;
    }
}
