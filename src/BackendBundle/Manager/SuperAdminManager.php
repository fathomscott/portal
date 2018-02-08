<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\SuperAdmin;
use BackendBundle\Entity\User;
use BackendBundle\Repository\SuperAdminRepository;
use BackendBundle\Security\Generator;
use BackendBundle\User\AccountTypeOptions;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class SuperAdminManager
 */
class SuperAdminManager
{
    /**
     * @var \BackendBundle\Repository\SuperAdminRepository
     */
    private $superAdminRepository;

    /**
     * @var Generator
     */
    protected $securityGenerator;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param SuperAdminRepository $superAdminRepository
     * @param Generator            $generator
     * @param Paginator            $paginator
     */
    public function __construct(
        $superAdminRepository,
        Generator $generator,
        Paginator $paginator
    ) {
        $this->superAdminRepository = $superAdminRepository;
        $this->securityGenerator = $generator;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->superAdminRepository->find($id);
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function getFindAll()
    {
        return $this->superAdminRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|SuperAdmin
     */
    public function findOneBy($param)
    {
        return $this->superAdminRepository->findOneBy($param);
    }

    /**
     * @param SuperAdmin $user
     * @param bool       $isNew
     * @return SuperAdmin
     */
    public function save(SuperAdmin $user, $isNew = false)
    {
        if ($isNew) {
            $salt = $this->securityGenerator->generateSalt();
            $user->setSalt($salt);
            $user->setStatus(User::STATUS_ACTIVE);
            $user->setAccountType(AccountTypeOptions::SUPER_ADMIN);
            $user->setRoles(['ROLE_SUPER_ADMIN']);
        }

        if ($user->getPlainPassword()) {
            $user->setPassword($this->securityGenerator->encodePassword($user->getPlainPassword(), $user->getSalt()));
        }

        $this->superAdminRepository->save($user);

        return $user;
    }

    /**
     * @param SuperAdmin $superAdmin
     */
    public function remove(SuperAdmin $superAdmin)
    {
        $this->superAdminRepository->remove($superAdmin);
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
        $queryBuilder = $this->superAdminRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
