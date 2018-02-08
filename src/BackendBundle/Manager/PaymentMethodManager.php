<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\PaymentMethod;
use BackendBundle\Repository\PaymentMethodRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class PaymentMethodManager
 */
class PaymentMethodManager
{
    /**
     * @var PaymentMethodRepository
     */
    private $paymentMethodRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param PaymentMethodRepository $paymentMethodRepository
     * @param Paginator               $paginator
     */
    public function __construct(
        PaymentMethodRepository $paymentMethodRepository,
        Paginator $paginator
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|PaymentMethod
     */
    public function find($id)
    {
        return $this->paymentMethodRepository->find($id);
    }

    /**
     * @return PaymentMethod[]
     */
    public function getFindAll()
    {
        return $this->paymentMethodRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|PaymentMethod
     */
    public function findOneBy($param)
    {
        return $this->paymentMethodRepository->findOneBy($param);
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return PaymentMethod
     */
    public function save(PaymentMethod $paymentMethod)
    {
        $this->paymentMethodRepository->save($paymentMethod);

        return $paymentMethod;
    }

    /**
     * @param PaymentMethod $paymentMethod
     */
    public function remove(PaymentMethod $paymentMethod)
    {
        $this->paymentMethodRepository->remove($paymentMethod);
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
        $queryBuilder = $this->paymentMethodRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
