<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Team;
use BackendBundle\Entity\TeamMember;
use BackendBundle\Repository\TeamMemberRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class TeamMemberManager
 */
class TeamMemberManager
{
    /**
     * @var TeamMemberRepository
     */
    private $teamMemberRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param TeamMemberRepository $teamMemberRepository
     * @param Paginator            $paginator
     */
    public function __construct(
        TeamMemberRepository $teamMemberRepository,
        Paginator $paginator
    ) {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|TeamMember
     */
    public function find($id)
    {
        return $this->teamMemberRepository->find($id);
    }

    /**
     * @return TeamMember[]
     */
    public function getFindAll()
    {
        return $this->teamMemberRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|TeamMember
     */
    public function findOneBy($param)
    {
        return $this->teamMemberRepository->findOneBy($param);
    }

    /**
     * @param TeamMember $teamMember
     * @return TeamMember
     */
    public function save(TeamMember $teamMember)
    {
        $this->teamMemberRepository->save($teamMember);

        return $teamMember;
    }

    /**
     * @param TeamMember $teamMember
     */
    public function remove(TeamMember $teamMember)
    {
        $this->teamMemberRepository->remove($teamMember);
    }


    /**
     * @param Team    $team
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllByTeamPaginator(Team $team, $page, $filters = null, $limit = 50, $sortFieldName = "a.teamLeader", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->teamMemberRepository->getFindAllByTeamQueryBuilder($team, $filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
