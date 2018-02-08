<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\User;
use BackendBundle\Repository\AgentRepository;
use BackendBundle\Security\Generator;
use BackendBundle\User\AccountTypeOptions;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;

/**
 * Class AgentManager
 */
class AgentManager
{
    /**
     * @var \BackendBundle\Repository\AgentRepository
     */
    private $agentRepository;

    /**
     * @var Generator
     */
    protected $securityGenerator;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param AgentRepository $agentRepository
     * @param Generator       $generator
     * @param Paginator       $paginator
     */
    public function __construct(
        AgentRepository $agentRepository,
        Generator $generator,
        Paginator $paginator
    ) {
        $this->agentRepository = $agentRepository;
        $this->securityGenerator = $generator;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->agentRepository->find($id);
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function getFindAll()
    {
        return $this->agentRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|Agent
     */
    public function findOneBy($param)
    {
        return $this->agentRepository->findOneBy($param);
    }

    /**
     * @param Agent $user
     * @param bool  $isNew
     * @return Agent
     */
    public function save(Agent $user, $isNew = false)
    {
        if ($isNew) {
            $salt = $this->securityGenerator->generateSalt();
            $user->setSalt($salt);
            $user->setStatus(User::STATUS_ACTIVE);
            $user->setAccountType(AccountTypeOptions::AGENT);
            $user->setRoles(['ROLE_AGENT']);
        }

        if ($user->getPlainPassword()) {
            $user->setPassword($this->securityGenerator->encodePassword($user->getPlainPassword(), $user->getSalt()));
        }

        $this->agentRepository->save($user);

        return $user;
    }

    /**
     * @param Agent $agent
     */
    public function remove(Agent $agent)
    {
        $this->agentRepository->remove($agent);
    }

    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "asc")
    {
        $queryBuilder = $this->agentRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @return \BackendBundle\Entity\Agent[]
     */
    public function getAgentsWithExpiringDocuments()
    {
        return $this->agentRepository->getAgentsWithExpiringDocuments();
    }

    /**
     * @return array|\BackendBundle\Entity\Agent[]
     */
    public function getAgentsWithMLSDuesRequiredDistricts()
    {
        return $this->agentRepository->getAgentsWithMLSDuesRequiredDistricts();
    }
}
