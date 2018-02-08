<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\District;
use BackendBundle\Entity\Transaction;
use BackendBundle\Repository\TransactionRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;

/**
 * Class TransactionManager
 */
class TransactionManager
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param TransactionRepository $transactionRepository
     * @param Paginator             $paginator
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        Paginator $paginator
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|Transaction
     */
    public function find($id)
    {
        return $this->transactionRepository->find($id);
    }

    /**
     * @return Transaction[]
     */
    public function getFindAll()
    {
        return $this->transactionRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|Transaction
     */
    public function findOneBy($param)
    {
        return $this->transactionRepository->findOneBy($param);
    }

    /**
     * @param Transaction $transaction
     * @return Transaction
     */
    public function save(Transaction $transaction)
    {
        $this->transactionRepository->save($transaction);

        return $transaction;
    }

    /**
     * @param Transaction $transaction
     */
    public function remove(Transaction $transaction)
    {
        $this->transactionRepository->remove($transaction);
    }

    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->transactionRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param Agent   $agent
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllByAgentPaginator(Agent $agent, $page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->transactionRepository->getFindAllByAgentQueryBuilder($agent, $filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindVariableMlsDuesPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->transactionRepository->getFindVariableMlsDuesQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param Agent|null $agent
     * @return array|Transaction
     */
    public function getFixedMLSDuesPendingTransactions(Agent $agent = null)
    {
        return $this->transactionRepository->getFixedMLSDuesPendingTransactions($agent);
    }

    /**
     * @param Agent $agent
     * @return Agent
     */
    public function createDistrictPendingTransactionsForAgent(Agent $agent)
    {
        foreach ($agent->getDistricts() as $district) {
            if ($district->isMLSDuesRequired()) {
                $transaction = new Transaction();
                $transaction->setStatus(Transaction::STATUS_PENDING);
                $transaction->setUser($agent);
                $transaction->setDistrict($district);

                if ($district->getMLSDuesType() === District::MLS_DUES_TYPE_FIXED) {
                    $transaction->setNotes(sprintf('MLS dues for %s', $district->getName()));
                    $transaction->setAmount($district->getMLSFee());
                } else {
                    $transaction->setNotes(sprintf('Pending transaction for district: %s', $district->getName()));
                    $transaction->setAmount(0);
                }

                $agent->addTransaction($transaction);
            }
        }

        return $agent;
    }
}
