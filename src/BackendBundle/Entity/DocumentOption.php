<?php
namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DocumentOption
 * @UniqueEntity("name")
 */
class DocumentOption
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
     * @var boolean
     */
    private $required = false;

    /**
     * @var boolean
     */
    private $expirationDateRequired = false;

    /**
     * @var ArrayCollection
     */
    private $documents;

    /**
     * DocumentOption constructor.
     */
    public function __construct()
    {
        $this->documents = new ArrayCollection();
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
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return boolean
     */
    public function isExpirationDateRequired()
    {
        return $this->expirationDateRequired;
    }

    /**
     * @param boolean $expirationDateRequired
     */
    public function setExpirationDateRequired($expirationDateRequired)
    {
        $this->expirationDateRequired = $expirationDateRequired;
    }

    /**
     * @return ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }
}
