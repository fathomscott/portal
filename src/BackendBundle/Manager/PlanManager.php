<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Plan;
use BackendBundle\Repository\PlanRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class PlanManager
 */
class PlanManager
{
    /**
     * @var PlanRepository
     */
    private $planRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param PlanRepository $planRepository
     * @param Paginator      $paginator
     */
    public function __construct(
        PlanRepository $planRepository,
        Paginator $paginator
    ) {
        $this->planRepository = $planRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|Plan
     */
    public function find($id)
    {
        return $this->planRepository->find($id);
    }

    /**
     * @return Plan[]
     */
    public function getFindAll()
    {
        return $this->planRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|Plan
     */
    public function findOneBy($param)
    {
        return $this->planRepository->findOneBy($param);
    }

    /**
     * @param Plan $plan
     * @return Plan
     */
    public function save(Plan $plan)
    {
        $this->planRepository->save($plan);

        return $plan;
    }

    /**
     * @param Plan $plan
     */
    public function remove(Plan $plan)
    {
        $this->planRepository->remove($plan);
    }
    
    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindActivePublicPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.monthlyPrice", $sortFieldDirection = "asc")
    {
        $queryBuilder = $this->planRepository->getFindActivePublicQueryBuilder($filters);

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
    public function getFindAllPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->planRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
