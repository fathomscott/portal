<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\District;
use BackendBundle\Repository\DistrictRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class DistrictManager
 */
class DistrictManager
{
    /**
     * @var DistrictRepository
     */
    private $districtRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param DistrictRepository $districtRepository
     * @param Paginator          $paginator
     */
    public function __construct(
        DistrictRepository $districtRepository,
        Paginator $paginator
    ) {
        $this->districtRepository = $districtRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|District
     */
    public function find($id)
    {
        return $this->districtRepository->find($id);
    }

    /**
     * @return District[]
     */
    public function getFindAll()
    {
        return $this->districtRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|District
     */
    public function findOneBy($param)
    {
        return $this->districtRepository->findOneBy($param);
    }

    /**
     * @param District $district
     * @return District
     */
    public function save(District $district)
    {
        $this->districtRepository->save($district);

        return $district;
    }

    /**
     * @param District $district
     */
    public function remove(District $district)
    {
        $this->districtRepository->remove($district);
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
        $queryBuilder = $this->districtRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
