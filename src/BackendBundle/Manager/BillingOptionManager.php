<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\BillingOption;
use BackendBundle\Repository\BillingOptionRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class BillingOptionManager
 */
class BillingOptionManager
{
    /**
     * @var BillingOptionRepository
     */
    private $billingOptionRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param BillingOptionRepository $billingOptionRepository
     * @param Paginator               $paginator
     */
    public function __construct(
        BillingOptionRepository $billingOptionRepository,
        Paginator $paginator
    ) {
        $this->billingOptionRepository = $billingOptionRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|BillingOption
     */
    public function find($id)
    {
        return $this->billingOptionRepository->find($id);
    }

    /**
     * @return BillingOption[]
     */
    public function getFindAll()
    {
        return $this->billingOptionRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|BillingOption
     */
    public function findOneBy($param)
    {
        return $this->billingOptionRepository->findOneBy($param);
    }

    /**
     * @param BillingOption $billingOption
     * @return BillingOption
     */
    public function save(BillingOption $billingOption)
    {
        $this->billingOptionRepository->save($billingOption);

        return $billingOption;
    }

    /**
     * @param BillingOption $billingOption
     */
    public function remove(BillingOption $billingOption)
    {
        $this->billingOptionRepository->remove($billingOption);
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
        $queryBuilder = $this->billingOptionRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
