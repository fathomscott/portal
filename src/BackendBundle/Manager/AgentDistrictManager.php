<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\AgentDistrict;
use BackendBundle\Repository\AgentDistrictRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class AgentDistrictManager
 */
class AgentDistrictManager
{
    /**
     * @var AgentDistrictRepository
     */
    private $agentDistrictRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param AgentDistrictRepository $agentDistrictRepository
     * @param Paginator               $paginator
     */
    public function __construct(
        AgentDistrictRepository $agentDistrictRepository,
        Paginator $paginator
    ) {
        $this->agentDistrictRepository = $agentDistrictRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|AgentDistrict
     */
    public function find($id)
    {
        return $this->agentDistrictRepository->find($id);
    }

    /**
     * @return AgentDistrict[]
     */
    public function getFindAll()
    {
        return $this->agentDistrictRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|AgentDistrict
     */
    public function findOneBy($param)
    {
        return $this->agentDistrictRepository->findOneBy($param);
    }

    /**
     * @param AgentDistrict $agentDistrict
     * @return AgentDistrict
     */
    public function save(AgentDistrict $agentDistrict)
    {
        $this->agentDistrictRepository->save($agentDistrict);

        return $agentDistrict;
    }

    /**
     * @param AgentDistrict $agentDistrict
     */
    public function remove(AgentDistrict $agentDistrict)
    {
        $this->agentDistrictRepository->remove($agentDistrict);
    }
}
