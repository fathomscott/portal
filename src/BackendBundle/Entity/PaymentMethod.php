<?php
namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * PaymentMethod
 */
class PaymentMethod
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \BackendBundle\Entity\User
     */
    private $user;

    /**
     * @var ArrayCollection|Transaction[]
     */
    private $transactions;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Add transactions
     *
     * @param Transaction $transactions
     * @return PaymentMethod
     */
    public function addTransaction(Transaction $transactions)
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions
     *
     * @param Transaction $transactions
     */
    public function removeTransaction(Transaction $transactions)
    {
        $this->transactions->removeElement($transactions);
    }

    /**
     * @return Transaction[]|ArrayCollection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
