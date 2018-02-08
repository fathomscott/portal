<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\LicenseCategory;
use BackendBundle\Repository\LicenseCategoryRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class LicenseCategoryManager
 */
class LicenseCategoryManager
{
    /**
     * @var LicenseCategoryRepository
     */
    private $licenseCategoryRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param LicenseCategoryRepository $licenseCategoryRepository
     * @param Paginator                 $paginator
     */
    public function __construct(
        LicenseCategoryRepository $licenseCategoryRepository,
        Paginator $paginator
    ) {
        $this->licenseCategoryRepository = $licenseCategoryRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|LicenseCategory
     */
    public function find($id)
    {
        return $this->licenseCategoryRepository->find($id);
    }

    /**
     * @return LicenseCategory[]
     */
    public function getFindAll()
    {
        return $this->licenseCategoryRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|LicenseCategory
     */
    public function findOneBy($param)
    {
        return $this->licenseCategoryRepository->findOneBy($param);
    }

    /**
     * @param LicenseCategory $licenseCategory
     * @return LicenseCategory
     */
    public function save(LicenseCategory $licenseCategory)
    {
        $this->licenseCategoryRepository->save($licenseCategory);

        return $licenseCategory;
    }

    /**
     * @param LicenseCategory $licenseCategory
     */
    public function remove(LicenseCategory $licenseCategory)
    {
        $this->licenseCategoryRepository->remove($licenseCategory);
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
        $queryBuilder = $this->licenseCategoryRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
