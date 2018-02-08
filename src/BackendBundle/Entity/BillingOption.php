<?php
namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * BillingOption
 */
class BillingOption
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
     * @var ArrayCollection|Transaction[]
     */
    private $transactions;

    /**
     * BillingOption constructor.
     */
    public function __construct()
    {
        $this->transactions = new ArrayCollection();
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
     * @return Transaction[]|ArrayCollection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
