<?php
namespace BackendBundle\Event;

use BackendBundle\Entity\Agent;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ForgotPasswordEvent
 */
class ExpiringDocumentAlertEvent extends Event
{
    /**
     * @var Agent
     */
    protected $agent;

    /**
     * @var string
     */
    protected $onboardingEmail;

    /**
     * ExpiringDocumentAlertEvent constructor.
     * @param Agent  $agent
     * @param string $onboardingEmail
     */
    public function __construct(Agent $agent, $onboardingEmail)
    {
        $this->agent = $agent;
        $this->onboardingEmail = $onboardingEmail;
    }

    /**
     * @return Agent
     */
    final public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @return string
     */
    final public function getOnboardingEmail()
    {
        return $this->onboardingEmail;
    }
}
