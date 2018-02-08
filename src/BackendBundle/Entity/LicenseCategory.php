<?php
namespace BackendBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * LicenseCategory
 * @UniqueEntity("name")
 */
class LicenseCategory
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
}
