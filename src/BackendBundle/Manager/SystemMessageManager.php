<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\SystemMessage;
use BackendBundle\Entity\User;
use BackendBundle\Repository\SystemMessageRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class SystemMessageManager
 */
class SystemMessageManager
{
    /**
     * @var SystemMessageRepository
     */
    private $systemMessageRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param SystemMessageRepository $systemMessageRepository
     * @param Paginator               $paginator
     */
    public function __construct(
        SystemMessageRepository $systemMessageRepository,
        Paginator $paginator
    ) {
        $this->systemMessageRepository = $systemMessageRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|SystemMessage
     */
    public function find($id)
    {
        return $this->systemMessageRepository->find($id);
    }

    /**
     * @return SystemMessage[]
     */
    public function getFindAll()
    {
        return $this->systemMessageRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|SystemMessage
     */
    public function findOneBy($param)
    {
        return $this->systemMessageRepository->findOneBy($param);
    }

    /**
     * @param SystemMessage $systemMessage
     * @return SystemMessage
     */
    public function save(SystemMessage $systemMessage)
    {
        $this->systemMessageRepository->save($systemMessage);

        return $systemMessage;
    }

    /**
     * @param SystemMessage $systemMessage
     */
    public function remove(SystemMessage $systemMessage)
    {
        $this->systemMessageRepository->remove($systemMessage);
    }


    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.startDate", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->systemMessageRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUnreadMessages(User $user)
    {
        return $this->systemMessageRepository->getUnreadMessages($user);
    }

    /**
     * @param User          $user
     * @param SystemMessage $systemMessage
     * @return bool
     */
    public function messageAlreadyDismissed(User $user, SystemMessage $systemMessage)
    {
        $systemMessage = $this->systemMessageRepository->messageAlreadyDismissed($user, $systemMessage);

        return $systemMessage instanceof SystemMessage;
    }

    /**
     * @param User          $user
     * @param SystemMessage $systemMessage
     */
    public function dismissMessage(User $user, SystemMessage $systemMessage)
    {
        $systemMessage->addUser($user);
        $this->save($systemMessage);
    }
}
