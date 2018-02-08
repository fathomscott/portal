<?php
namespace BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * SystemDefault
 */
class SystemDefault
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $onboardingEmail;

    /**
     * @var integer
     * @Assert\Range(min="0", max="1000")
     * @Assert\NotBlank()
     */
    private $dunningDays = 0;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getOnboardingEmail()
    {
        return $this->onboardingEmail;
    }

    /**
     * @param string $onboardingEmail
     */
    public function setOnboardingEmail($onboardingEmail)
    {
        $this->onboardingEmail = $onboardingEmail;
    }

    /**
     * @return int
     */
    public function getDunningDays()
    {
        return $this->dunningDays;
    }

    /**
     * @param int $dunningDays
     */
    public function setDunningDays($dunningDays)
    {
        $this->dunningDays = $dunningDays;
    }
}
