<?php
namespace BackendBundle\Entity;

/**
 * Class TeamMember
 * @package BackendBundle\Entity
 */
class TeamMember
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var Agent
     */
    private $agent;

    /**
     * @var Team
     */
    private $team;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $invitationEmail;

    /**
     * @var boolean
     */
    private $teamLeader = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return boolean
     */
    public function isTeamLeader()
    {
        return $this->teamLeader;
    }

    /**
     * @param boolean $teamLeader
     */
    public function setTeamLeader($teamLeader)
    {
        $this->teamLeader = $teamLeader;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getInvitationEmail()
    {
        return $this->invitationEmail;
    }

    /**
     * @param string $invitationEmail
     */
    public function setInvitationEmail($invitationEmail)
    {
        $this->invitationEmail = $invitationEmail;
    }
}
