<?php
namespace BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * CreditCard
 */
class CreditCard extends PaymentMethod
{
    /**
     * @var string
     */
    private $lastFour;

    /**
     * @var string
     */
    private $cardToken;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var string
     * @Assert\Luhn()
     */
    private $cardNumber;

    /**
     * @var integer
     * @Assert\Regex("/^\d{3,5}$/")
     */
    private $cvv;

    /**
     * @var integer
     * @Assert\Range(min="1", max="12")
     */
    private $expMonth;

    /**
     * @Assert\Range(min="17", max="99")
     */
    private $expYear;

    /**
     * @return string
     */
    public function getLastFour()
    {
        return $this->lastFour;
    }

    /**
     * @param string $lastFour
     */
    public function setLastFour($lastFour)
    {
        $this->lastFour = $lastFour;
    }

    /**
     * @return string
     */
    public function getCardToken()
    {
        return $this->cardToken;
    }

    /**
     * @param string $cardToken
     */
    public function setCardToken($cardToken)
    {
        $this->cardToken = $cardToken;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param string $cardNumber
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @return int
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * @param int $cvv
     */
    public function setCvv($cvv)
    {
        $this->cvv = $cvv;
    }

    /**
     * @return int
     */
    public function getExpMonth()
    {
        return $this->expMonth;
    }

    /**
     * @param int $expMonth
     */
    public function setExpMonth($expMonth)
    {
        $this->expMonth = $expMonth;
    }

    /**
     * @return mixed
     */
    public function getExpYear()
    {
        return $this->expYear;
    }

    /**
     * @param mixed $expYear
     */
    public function setExpYear($expYear)
    {
        $this->expYear = $expYear;
    }
}
