<?php
namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * State
 * @UniqueEntity(fields={"name", "state"}, message="This district name is already in use on that State.")
 */
class District
{
    const MLS_DUES_TYPE_FIXED = 0;
    const MLS_DUES_TYPE_VARIABLE = 1;

    public static $duesTypes = [
        'labels.fixed' => self::MLS_DUES_TYPE_FIXED,
        'labels.variable' => self::MLS_DUES_TYPE_VARIABLE,
    ];

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var State
     */
    private $state;

    /**
     * @var boolean
     */
    private $MLSDuesRequired = false;

    /**
     * @var integer
     */
    private $MLSDuesType = self::MLS_DUES_TYPE_FIXED;

    /**
     * @var float
     */
    private $MLSFee;

    /**
     * @var ArrayCollection|AgentDistrict[]
     */
    private $agentDistricts;

    /**
     * @var ArrayCollection|Team[]
     */
    private $teams;

    /**
     * @var District[]
     */
    private $transactions;

    /**
     * District constructor.
     */
    public function __construct()
    {
        $this->agentDistricts = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->transactions = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return ArrayCollection|AgentDistrict[]
     */
    public function getAgentDistricts()
    {
        return $this->agentDistricts;
    }

    /**
     * @return Agent[]
     */
    public function getAgents()
    {
        $districts = [];
        foreach ($this->agentDistricts as $agentDistrict) {
            $districts[] = $agentDistrict->getAgent();
        }

        return $districts;
    }

    /**
     * @return Team[]|ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @return District[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @return boolean
     */
    public function isMLSDuesRequired()
    {
        return $this->MLSDuesRequired;
    }

    /**
     * @param boolean $MLSDuesRequired
     */
    public function setMLSDuesRequired($MLSDuesRequired)
    {
        $this->MLSDuesRequired = $MLSDuesRequired;
    }

    /**
     * @return int
     */
    public function getMLSDuesType()
    {
        return $this->MLSDuesType;
    }

    /**
     * @param int $MLSDuesType
     */
    public function setMLSDuesType($MLSDuesType)
    {
        $this->MLSDuesType = $MLSDuesType;
    }

    /**
     * @return string|null
     */
    public function getMLSDuesTypeName()
    {
        if (in_array($this->getMLSDuesType(), self::$duesTypes, true)) {
            return array_search($this->getMLSDuesType(), self::$duesTypes);
        }

        return null;
    }

    /**
     * @return float
     */
    public function getMLSFee()
    {
        return $this->MLSFee;
    }

    /**
     * @param float $MLSFee
     */
    public function setMLSFee($MLSFee)
    {
        $this->MLSFee = $MLSFee;
    }
}
