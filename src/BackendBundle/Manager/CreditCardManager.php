<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\CreditCard;
use BackendBundle\Repository\CreditCardRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class CreditCardManager
 */
class CreditCardManager
{
    /**
     * @var CreditCardRepository
     */
    private $creditCardRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param CreditCardRepository $creditCardRepository
     * @param Paginator            $paginator
     */
    public function __construct(
        CreditCardRepository $creditCardRepository,
        Paginator $paginator
    ) {
        $this->creditCardRepository = $creditCardRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|CreditCard
     */
    public function find($id)
    {
        return $this->creditCardRepository->find($id);
    }

    /**
     * @return CreditCard[]
     */
    public function getFindAll()
    {
        return $this->creditCardRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|CreditCard
     */
    public function findOneBy($param)
    {
        return $this->creditCardRepository->findOneBy($param);
    }

    /**
     * @param CreditCard $creditCard
     * @return CreditCard
     */
    public function save(CreditCard $creditCard)
    {
        


        $this->creditCardRepository->save($creditCard);

        return $creditCard;
    }

    /**
     * @param CreditCard $creditCard
     */
    public function remove(CreditCard $creditCard)
    {
        $this->creditCardRepository->remove($creditCard);
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
        $queryBuilder = $this->creditCardRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
