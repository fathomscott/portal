<?php
namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Team
 */
class Team
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var District
     */
    private $district;

    /**
     * @var ArrayCollection
     */
    private $members;

    /**
     * Team constructor.
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return ArrayCollection|TeamMember[]
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param TeamMember $member
     */
    public function addMember(TeamMember $member)
    {
        $this->members->add($member);
    }

    /**
     * @param TeamMember $teamMember
     */
    public function removeMember(TeamMember $teamMember)
    {
        $this->members->removeElement($teamMember);
    }

    /**
     * @param Agent $agent
     * @return bool
     */
    public function isTeamLeader(Agent $agent)
    {
        $isTeamLeader = false;
        foreach ($this->getMembers() as $teamMember) {
            if ($teamMember->getAgent() === $agent) {
                $isTeamLeader = $teamMember->isTeamLeader();
                break;
            }
        }

        return $isTeamLeader;
    }

    /**
     * @param Agent $agent
     * @return bool
     */
    public function isTeamMember(Agent $agent)
    {
        $isMember = false;
        foreach ($this->getMembers() as $teamMember) {
            if ($teamMember->getAgent() === $agent) {
                $isMember = true;
                break;
            }
        }

        return $isMember;
    }

    /**
     * @return District
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param District $district
     */
    public function setDistrict(District $district)
    {
        $this->district = $district;
    }
}
