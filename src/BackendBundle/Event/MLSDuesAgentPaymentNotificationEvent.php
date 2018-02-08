<?php
namespace BackendBundle\Event;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\District;
use BackendBundle\Entity\Transaction;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MLSDuesAgentPaymentNotificationEvent
 */
class MLSDuesAgentPaymentNotificationEvent extends Event
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * MLSDuesAgentPaymentNotificationEvent constructor.
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return Transaction
     */
    final public function getTransaction()
    {
        return $this->transaction;
    }
}
