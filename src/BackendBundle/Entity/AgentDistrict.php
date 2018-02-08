<?php

namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgentDistrict
 */
class AgentDistrict
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
     * @var District
     */
    private $district;

    /**
     * @var boolean
     */
    private $districtDirector = false;

    /**
     * @var boolean
     */
    private $primaryMarket = true;

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

    /**
     * @return boolean
     */
    public function isDistrictDirector()
    {
        return $this->districtDirector;
    }

    /**
     * @return boolean
     */
    public function isPrimaryMarket()
    {
        return $this->primaryMarket;
    }

    /**
     * @param boolean $districtDirector
     */
    public function setDistrictDirector($districtDirector)
    {
        $this->districtDirector = $districtDirector;
    }

    /**
     * @param boolean $primaryMarket
     */
    public function setPrimaryMarket($primaryMarket)
    {
        $this->primaryMarket = $primaryMarket;
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
