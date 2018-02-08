<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\AgentNote;
use BackendBundle\Entity\User;
use BackendBundle\Repository\AgentNoteRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class AgentNoteManager
 */
class AgentNoteManager
{
    /**
     * @var AgentNoteRepository
     */
    private $agentNoteRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param AgentNoteRepository $agentNoteRepository
     * @param Paginator           $paginator
     */
    public function __construct(
        AgentNoteRepository $agentNoteRepository,
        Paginator $paginator
    ) {
        $this->agentNoteRepository = $agentNoteRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|AgentNote
     */
    public function find($id)
    {
        return $this->agentNoteRepository->find($id);
    }

    /**
     * @return AgentNote[]
     */
    public function getFindAll()
    {
        return $this->agentNoteRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|AgentNote
     */
    public function findOneBy($param)
    {
        return $this->agentNoteRepository->findOneBy($param);
    }

    /**
     * @param AgentNote $agentNote
     * @return AgentNote
     */
    public function save(AgentNote $agentNote)
    {
        $this->agentNoteRepository->save($agentNote);

        return $agentNote;
    }

    /**
     * @param AgentNote $agentNote
     */
    public function remove(AgentNote $agentNote)
    {
        $this->agentNoteRepository->remove($agentNote);
    }


    /**
     * @param Agent   $agent
     * @param User    $user
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllByAgentPaginator(Agent $agent, User $user, $page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->agentNoteRepository->getFindAllByAgentPaginator($agent, $user, $filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
