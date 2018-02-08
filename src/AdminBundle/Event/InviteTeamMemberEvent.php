<?php
namespace AdminBundle\Event;

use BackendBundle\Entity\TeamMember;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class InviteTeamMemberEvent
 */
class InviteTeamMemberEvent extends Event
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var TeamMember
     */
    private $teamMember;

    /**
     * InviteTeamMemberEvent constructor.
     * @param TeamMember $teamMember
     * @param string     $message
     */
    public function __construct(TeamMember $teamMember, $message)
    {
        $this->teamMember = $teamMember;
        $this->message = $message;
    }

    /**
     * @return string
     */
    final public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return TeamMember
     */
    final public function getTeamMember()
    {
        return $this->teamMember;
    }
}
