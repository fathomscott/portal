<?php
namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Validator\Constraints\ConstraintAgentDistrict as AgentDistrictValidator;

/**
 * Agent
 * @AgentDistrictValidator
 */
class Agent extends User
{
    /**
     * @var string
     */
    private $legalName;
 
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @var integer
     * @Assert\NotBlank()
     */
    private $state;

    /**
     * @var integer
     * @Assert\NotBlank()
     */
    private $zip;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $personalEmail;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     */
    private $birthDate;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     */
    private $joinedDate;

    /**
     * @var string
     */
    private $socialSecurityNumber;

    /**
     * @var string
     */
    private $ein;

    /**
     * @var string
     */
    private $pastFirm;

    /**
     * Real Estate License expiration date.
     * @var \DateTime
     */
    private $RELExpirationDate;

    /**
     * @var \DateTime
     */
    private $insuranceExpirationDate;

    /**
     * @var boolean
     */
    private $isMentor = false;

    /**
     * @var boolean
     */
    private $isMentee = false;

    /**
     * @var ArrayCollection
     */
    private $members;

    /**
     * @var ArrayCollection
     */
    private $agentNotes;

    /**
     * @var ArrayCollection|AgentDistrict[]
     */
    private $agentDistricts;

    /**
     * @var ArrayCollection|Document[]
     */
    private $documents;

    /**
     * Agent constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->members = new ArrayCollection();
        $this->agentNotes = new ArrayCollection();
        $this->agentDistricts = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getLegalName()
    {
        return $this->legalName;
    }

    /**
     * @param string $legalName
     */
    public function setLegalName($legalName)
    {
        $this->legalName = $legalName;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getPersonalEmail()
    {
        return $this->personalEmail;
    }

    /**
     * @param string $personalEmail
     */
    public function setPersonalEmail($personalEmail)
    {
        $this->personalEmail = $personalEmail;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return \DateTime
     */
    public function getJoinedDate()
    {
        return $this->joinedDate;
    }

    /**
     * @param \DateTime $joinedDate
     */
    public function setJoinedDate($joinedDate)
    {
        $this->joinedDate = $joinedDate;
    }

    /**
     * @return string
     */
    public function getSocialSecurityNumber()
    {
        return $this->socialSecurityNumber;
    }

    /**
     * @param string $socialSecurityNumber
     */
    public function setSocialSecurityNumber($socialSecurityNumber)
    {
        $this->socialSecurityNumber = $socialSecurityNumber;
    }

    /**
     * @return string
     */
    public function getEin()
    {
        return $this->ein;
    }

    /**
     * @param string $ein
     */
    public function setEin($ein)
    {
        $this->ein = $ein;
    }

    /**
     * @return string
     */
    public function getPastFirm()
    {
        return $this->pastFirm;
    }

    /**
     * @param string $pastFirm
     */
    public function setPastFirm($pastFirm)
    {
        $this->pastFirm = $pastFirm;
    }

    /**
     * @return \DateTime
     */
    public function getRELExpirationDate()
    {
        return $this->RELExpirationDate;
    }

    /**
     * @param \DateTime $RELExpirationDate
     */
    public function setRELExpirationDate($RELExpirationDate)
    {
        $this->RELExpirationDate = $RELExpirationDate;
    }

    /**
     * @return \DateTime
     */
    public function getInsuranceExpirationDate()
    {
        return $this->insuranceExpirationDate;
    }

    /**
     * @param \DateTime $insuranceExpirationDate
     */
    public function setInsuranceExpirationDate($insuranceExpirationDate)
    {
        $this->insuranceExpirationDate = $insuranceExpirationDate;
    }

    /**
     * @return boolean
     */
    public function isIsMentor()
    {
        return $this->isMentor;
    }

    /**
     * @param boolean $isMentor
     */
    public function setIsMentor($isMentor)
    {
        $this->isMentor = $isMentor;
    }

    /**
     * @return boolean
     */
    public function isIsMentee()
    {
        return $this->isMentee;
    }

    /**
     * @param boolean $isMentee
     */
    public function setIsMentee($isMentee)
    {
        $this->isMentee = $isMentee;
    }

    /**
     * @return boolean
     */
    public function isDistrictDirector()
    {
        foreach ($this->agentDistricts as $agentDistrict) {
            if ($agentDistrict->isDistrictDirector()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @return ArrayCollection
     */
    public function getAgentNotes()
    {
        return $this->agentNotes;
    }

    /**
     * @return ArrayCollection|AgentDistrict[]
     */
    public function getAgentDistricts()
    {
        return $this->agentDistricts;
    }

    /**
     * @param AgentDistrict $agentDistrict
     */
    public function addAgentDistrict(AgentDistrict $agentDistrict)
    {
        if (!$this->agentDistricts->contains($agentDistrict)) {
            $agentDistrict->setAgent($this);
            $this->agentDistricts->add($agentDistrict);
        }
    }

    /**
     * @param AgentDistrict $agentDistrict
     */
    public function removeAgentDistrict(AgentDistrict $agentDistrict)
    {
        if ($this->agentDistricts->contains($agentDistrict)) {
            $this->agentDistricts->removeElement($agentDistrict);
        }
    }

    /**
     * Virtual getter for form
     *
     * @return District[]
     */
    public function getDistricts()
    {
        $districts = [];
        foreach ($this->agentDistricts as $agentDistrict) {
            $districts[] = $agentDistrict->getDistrict();
        }

        return $districts;
    }

    /**
     * @param Document $document
     */
    public function addDocument(Document $document)
    {
        $this->documents->add($document);
    }

    /**
     * @param Document $document
     */
    public function removeDocument(Document $document)
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
        }
    }

    /**
     * @return Document[]|ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return null|string
     */
    public function getTerminalPin()
    {
        if (false === $this->agentDistricts->first()) {
            return null;
        }

        return $this->agentDistricts->first()->getDistrict()->getState()->getTerminalPin();
    }

    /**
     * @return null|string
     */
    public function getMerchantId()
    {
        if (false === $this->agentDistricts->first()) {
            return null;
        }
        return $this->agentDistricts->first()->getDistrict()->getState()->getMerchantId();
    }
}
