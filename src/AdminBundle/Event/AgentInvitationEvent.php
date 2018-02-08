<?php
namespace AdminBundle\Event;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AgentInvitationEvent
 */
class AgentInvitationEvent extends Event
{
    /**
     * @var Agent
     */
    protected $agent;

    /**
     * @param User $agent
     */
    public function __construct(User $agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return Agent
     */
    final public function getAgent()
    {
        return $this->agent;
    }
}
