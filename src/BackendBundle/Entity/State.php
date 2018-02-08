<?php
namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * State
 * @UniqueEntity("name")
 * @UniqueEntity("code")
 */
class State
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $terminalPin;

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var ArrayCollection
     */
    private $districts;

    /**
     * State constructor.
     */
    public function __construct()
    {
        $this->districts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
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
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return ArrayCollection
     */
    public function getDistricts()
    {
        return $this->districts;
    }

    /**
     * @param District $district
     */
    public function addDistrict(District $district)
    {
        $this->districts->add($district);
    }

    /**
     * @param District $district
     */
    public function removeDistrict(District $district)
    {
        $this->districts->removeElement($district);
    }

    /**
     * @return string
     */
    public function getTerminalPin()
    {
        return $this->terminalPin;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param string $terminalPin
     */
    public function setTerminalPin($terminalPin)
    {
        $this->terminalPin = $terminalPin;
    }
    /**
     * @param string $merchantId
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }
}
