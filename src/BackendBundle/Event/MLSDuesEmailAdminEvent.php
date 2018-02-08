<?php
namespace BackendBundle\Event;

use BackendBundle\Entity\Transaction;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MLSDuesEmailAdminEvent
 */
class MLSDuesEmailAdminEvent extends Event
{
    /**
     * @var array|Transaction[]
     */
    private $fixedMLSDuesTransactions;

    /**
     * @var array|Transaction[]
     */
    private $variableMLSDuesTransactions;

    /**
     * MLSDuesEmailAdminEvent constructor.
     * @param array $fixedMLSDuesTransactions
     * @param array $variableMLSDuesTransactions
     */
    public function __construct(array $fixedMLSDuesTransactions, array $variableMLSDuesTransactions)
    {
        $this->fixedMLSDuesTransactions = $fixedMLSDuesTransactions;
        $this->variableMLSDuesTransactions = $variableMLSDuesTransactions;
    }

    /**
     * @return array|Transaction[]
     */
    final public function getFixedMLSDuesTransactions()
    {
        return $this->fixedMLSDuesTransactions;
    }

    /**
     * @return array|Transaction[]
     */
    final public function getVariableMLSDuesTransactions()
    {
        return $this->variableMLSDuesTransactions;
    }
}
