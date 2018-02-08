<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Email;
use BackendBundle\Repository\EmailRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class EmailManager
 */
class EmailManager
{
    /**
     * @var EmailRepository
     */
    private $emailRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param EmailRepository $emailRepository
     * @param Paginator       $paginator
     */
    public function __construct(
        EmailRepository $emailRepository,
        Paginator $paginator
    ) {
        $this->emailRepository = $emailRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->emailRepository->find($id);
    }

    /**
     * @return null|Email[]
     */
    public function getFindAll()
    {
        return $this->emailRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|Email
     */
    public function findOneBy($param)
    {
        return $this->emailRepository->findOneBy($param);
    }

    /**
     * @param Email $email
     * @return Email
     */
    public function save(Email $email)
    {
        $this->emailRepository->save($email);

        return $email;
    }

    /**
     * @param Email $email
     */
    public function remove(Email $email)
    {
        $this->emailRepository->remove($email);
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
        $queryBuilder = $this->emailRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
