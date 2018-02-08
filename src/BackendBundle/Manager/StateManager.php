<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\State;
use BackendBundle\Repository\StateRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class StateManager
 */
class StateManager
{
    /**
     * @var StateRepository
     */
    private $stateRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param StateRepository $stateRepository
     * @param Paginator       $paginator
     */
    public function __construct(
        StateRepository $stateRepository,
        Paginator $paginator
    ) {
        $this->stateRepository = $stateRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|State
     */
    public function find($id)
    {
        return $this->stateRepository->find($id);
    }

    /**
     * @return State[]
     */
    public function getFindAll()
    {
        return $this->stateRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|State
     */
    public function findOneBy($param)
    {
        return $this->stateRepository->findOneBy($param);
    }

    /**
     * @param State $state
     * @return State
     */
    public function save(State $state)
    {
        $this->stateRepository->save($state);

        return $state;
    }

    /**
     * @param State $state
     */
    public function remove(State $state)
    {
        $this->stateRepository->remove($state);
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
        $queryBuilder = $this->stateRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @return array
     */
    public function getAllStatesForAgentImporter()
    {
        $array = $this->stateRepository->getAllForAgentImporter();
        $states = [];
        foreach ($array as $row) {
            $stateName = strtolower(trim($row['name']));
            $states[$stateName]['id'] = $row['id'];
            $states[$stateName]['districts'] = [];

            foreach ($row['districts'] as $district) {
                $districtName = strtolower(trim($district['name']));
                $states[$row['name']]['districts'][$districtName] = $district['id'];
            }
        }

        return $states;
    }
}
