<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Admin;
use BackendBundle\Entity\User;
use BackendBundle\Repository\AdminRepository;
use BackendBundle\Security\Generator;
use BackendBundle\User\AccountTypeOptions;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class AdminManager
 */
class AdminManager
{
    /**
     * @var \BackendBundle\Repository\AdminRepository
     */
    private $adminRepository;

    /**
     * @var Generator
     */
    protected $securityGenerator;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param AdminRepository $adminRepository
     * @param Generator       $generator
     * @param Paginator       $paginator
     */
    public function __construct(
        AdminRepository $adminRepository,
        Generator $generator,
        Paginator $paginator
    ) {
        $this->adminRepository = $adminRepository;
        $this->securityGenerator = $generator;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->adminRepository->find($id);
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function getFindAll()
    {
        return $this->adminRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|Admin
     */
    public function findOneBy($param)
    {
        return $this->adminRepository->findOneBy($param);
    }

    /**
     * @param Admin $user
     * @param bool  $isNew
     * @return Admin
     */
    public function save(Admin $user, $isNew = false)
    {
        if ($isNew) {
            $salt = $this->securityGenerator->generateSalt();
            $user->setSalt($salt);
            $user->setStatus(User::STATUS_ACTIVE);
            $user->setAccountType(AccountTypeOptions::ADMIN);
            $user->setRoles(['ROLE_ADMIN']);
        }

        if ($user->getPlainPassword()) {
            $user->setPassword($this->securityGenerator->encodePassword($user->getPlainPassword(), $user->getSalt()));
        }

        $this->adminRepository->save($user);

        return $user;
    }

    /**
     * @param Admin $admin
     */
    public function remove(Admin $admin)
    {
        $this->adminRepository->remove($admin);
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
        $queryBuilder = $this->adminRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
