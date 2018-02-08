<?php
namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 */
class Transaction
{
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_DECLINED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_EXPIRED = 5;
    const STATUS_REFUNDED = 6;

    public static $statuses = array(
        'labels.pending'   => self::STATUS_PENDING,
        'labels.approved'  => self::STATUS_APPROVED,
        'labels.declined'  => self::STATUS_DECLINED,
        'labels.cancelled' => self::STATUS_CANCELLED,
        'labels.expired'   => self::STATUS_EXPIRED,
        'labels.refunded'  => self::STATUS_REFUNDED,
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var string
     */
    private $vendorId;

    /**
     * @var User
     */
    private $user;

    /**
     * @var PaymentMethod
     */
    private $paymentMethod;

    /**
     * @var District|null
     */
    private $district;

    /**
     * @var BillingOption|null
     */
    private $billingOption;

    /**
     * @return mixed|null
     */
    public function getStatusName()
    {
        if (in_array($this->status, self::$statuses, true)) {
            return array_search($this->status, self::$statuses);
        }

        return null;
    }

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

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     * @param string $vendorId
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param PaymentMethod $paymentMethod
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
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
     * @return BillingOption|null
     */
    public function getBillingOption()
    {
        return $this->billingOption;
    }

    /**
     * @param BillingOption|null $billingOption
     */
    public function setBillingOption($billingOption)
    {
        $this->billingOption = $billingOption;
    }
}
