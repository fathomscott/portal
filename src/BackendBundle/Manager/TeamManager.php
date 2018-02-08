<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Team;
use BackendBundle\Repository\TeamRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class TeamManager
 */
class TeamManager
{
    /**
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param TeamRepository $teamRepository
     * @param Paginator      $paginator
     */
    public function __construct(
        TeamRepository $teamRepository,
        Paginator $paginator
    ) {
        $this->teamRepository = $teamRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|Team
     */
    public function find($id)
    {
        return $this->teamRepository->find($id);
    }

    /**
     * @return Team[]
     */
    public function getFindAll()
    {
        return $this->teamRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|Team
     */
    public function findOneBy($param)
    {
        return $this->teamRepository->findOneBy($param);
    }

    /**
     * @param Team $team
     * @return Team
     */
    public function save(Team $team)
    {
        $this->teamRepository->save($team);

        return $team;
    }

    /**
     * @param Team $team
     */
    public function remove(Team $team)
    {
        $this->teamRepository->remove($team);
    }

    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllByPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->teamRepository->getFindAllByQueryBuilder($filters);

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
        $queryBuilder = $this->teamRepository->getFindAllByAgentQueryBuilder($agent, $filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
