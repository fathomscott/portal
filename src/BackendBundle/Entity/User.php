<?php
namespace BackendBundle\Entity;

use BackendBundle\User\AccountTypeOptions;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use BackendBundle\Entity\Referral;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword as AssertUserPassword;
use Serializable;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * User
 * @Vich\Uploadable
 */
class User implements AdvancedUserInterface, Serializable
{
    const STATUS_ACTIVE     = 1;
    const STATUS_INACTIVE   = 0;
    const STATUS_PENDING    = 2;

    public static $statuses = array(
        'labels.active'   => self::STATUS_ACTIVE,
        'labels.inactive' => self::STATUS_INACTIVE,
        'labels.pending'  => self::STATUS_PENDING,
    );

    public static $accountTypes = array(
        AccountTypeOptions::SUPER_ADMIN       => 'labels.super_admin',
        AccountTypeOptions::AGENT             => 'labels.agent',
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var string
     */
    private $accountType;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string
     */
    private $nickName;

    /**
     * @var string
     */
    private $middleName;

    /**
     * @var string
     */
    private $middleInitial;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @var string
     */
    private $confirmationToken;

    /**
     * @var \DateTime
     */
    private $expirationAt;

    /**
     * Unmapped, used for checking password in forms
     * @AssertUserPassword(groups={"ChangePassword"})
     */
    private $currentPassword;

    /**
     * @var string
     * @Assert\NotBlank(groups={"ChangePassword"})
     * @Assert\Length(min=6)
     */
    private $plainPassword;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="agent_head_shot", fileNameProperty="headShotName")
     *
     * @var File
     */
    private $headShotFile;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $headShotName;

    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var ArrayCollection|PaymentMethod[]
     */
    private $paymentMethods;

    /**
     * @var ArrayCollection|Transaction[]
     */
    private $transactions;

    /**
     * @var ArrayCollection|Referral[]
     */
    private $referredReferrals;

    /**
     * @var ArrayCollection|Referral[]
     */
    private $referringReferrals;

    /**
     * @var PersistentCollection|SystemMessage[]
     */
    private $dismissedSystemMessages;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->paymentMethods = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->referredReferrals = new ArrayCollection();
        $this->referringReferrals = new ArrayCollection();
        $this->dismissedSystemMessages = new ArrayCollection();
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setHeadShotFile(File $image = null)
    {
        $this->headShotFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return File
     */
    public function getHeadShotFile()
    {
        return $this->headShotFile;
    }

    /**
     * @param string $headShotName
     */
    public function setHeadShotName($headShotName)
    {
        $this->headShotName = $headShotName;
    }

    /**
     * @return string
     */
    public function getHeadShotName()
    {
        return $this->headShotName;
    }

    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function eraseCredentials()
    {
        return true;
    }


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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set nickName
     *
     * @param string $nickName
     * @return User
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * Get nickName
     *
     * @return string
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set middleInitial
     *
     * @param string $middleInitial
     * @return User
     */
    public function setMiddleInitial($middleInitial)
    {
        $this->middleInitial = $middleInitial;

        return $this;
    }

    /**
     * Get middleInitial
     *
     * @return string
     */
    public function getMiddleInitial()
    {
        return $this->middleInitial;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set suffix
     *
     * @param string $suffix
     * @return User
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Get suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getStatus() === User::STATUS_ACTIVE;
    }

    /**
     * @return mixed|null
     */
    public function getStatusName()
    {
        if (in_array($this->getStatus(), self::$statuses, true)) {
            return array_search($this->getStatus(), self::$statuses);
        }

        return null;
    }

    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set expirationAt
     *
     * @param \DateTime $expirationAt
     * @return User
     */
    public function setExpirationAt($expirationAt)
    {
        $this->expirationAt = $expirationAt;

        return $this;
    }

    /**
     * Get expirationAt
     *
     * @return \DateTime
     */
    public function getExpirationAt()
    {
        return $this->expirationAt;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s %s - %s', $this->firstName, $this->lastName, $this->email);
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return sprintf('%s %s - %s', $this->lastName, $this->firstName, $this->email);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $names = array_filter(
            array($this->firstName, $this->lastName),
            function ($element) {
                return (boolean) trim($element);
            }
        );

        return trim(implode(' ', $names));
    }

    /**
     * @return mixed
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }
    /**
     * @param mixed $currentPassword
     */
    public function setCurrentPassword($currentPassword)
    {
        $this->currentPassword = $currentPassword;
    }



    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }
    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }


    /**
     * Set accountType
     *
     * @param string $accountType
     * @return User
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * Get accountType
     *
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @return null
     */
    public function getAccountTypeName()
    {
        if (isset(self::$accountTypes[$this->getAccountType()])) {
            return self::$accountTypes[$this->getAccountType()];
        }

        return null;
    }
    

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param Subscription $subscription
     */
    public function setSubscription(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Add transactions
     *
     * @param Transaction $transactions
     * @return User
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

    /**
     * Add paymentMethods
     *
     * @param PaymentMethod $paymentMethods
     * @return User
     */
    public function addPaymentMethod(PaymentMethod $paymentMethods)
    {
        $this->paymentMethods[] = $paymentMethods;

        return $this;
    }

    /**
     * Remove paymentMethods
     *
     * @param PaymentMethod $paymentMethods
     */
    public function removePaymentMethod(PaymentMethod $paymentMethods)
    {
        $this->paymentMethods->removeElement($paymentMethods);
    }

    /**
     * Get paymentMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->status,
        ]);
    }


    /**
     * @return User|null
     */
    public function getReferringUser()
    {
        /** @var Referral $referral */
        $referral = $this->referredReferrals->first();

        if ($referral !== false) {
            return $referral->getReferringUser();
        }

        return null;
    }

    /**
     * @param User $user
     */
    public function setReferringUser(User $user = null)
    {
        /* one user can have only one referred user */
        $this->referredReferrals->clear();

        if (null !== $user) {
            $referral = new Referral();
            $referral->setReferredUser($this);
            $referral->setReferringUser($user);
            $referral->setCreated(new \DateTime('now'));

            $this->referredReferrals->add($referral);
        }
    }

    /**
     * @return ArrayCollection|Referral[]
     */
    public function getReferringReferrals()
    {
        return $this->referringReferrals;
    }

    /**
     * @param Referral $referredReferral
     *
     * @return User
     */
    public function addReferredReferral(Referral $referredReferral)
    {
        $this->referredReferrals[] = $referredReferral;

        return $this;
    }

    /**
     * @param Referral $referredReferral
     */
    public function removeReferredReferral(Referral $referredReferral)
    {
        $this->referredReferrals->removeElement($referredReferral);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReferredReferrals()
    {
        return $this->referredReferrals;
    }

    /**
     * @return SystemMessage[]|PersistentCollection
     */
    public function getDismissedSystemMessages()
    {
        return $this->dismissedSystemMessages;
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->status,
            ) = unserialize($serialized);
    }
}
