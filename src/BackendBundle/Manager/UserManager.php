<?php
namespace BackendBundle\Manager;

use DateTime;
use BackendBundle\Entity\User;
use BackendBundle\Repository\UserRepository;
use BackendBundle\Security\Generator;
use BackendBundle\User\AccountTypeOptions;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class UserManager
 */
class UserManager
{
    /**
     * @var \BackendBundle\Repository\UserRepository
     */
    private $userRepository;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var Generator
     */
    protected $securityGenerator;

     /**
     * @param UserRepository $userRepository
     * @param Generator      $securityGenerator
     * @param Paginator      $paginator
     */
    public function __construct(
        UserRepository $userRepository,
        Generator $securityGenerator,
        Paginator $paginator
    ) {
        $this->userRepository = $userRepository;
        $this->securityGenerator = $securityGenerator;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->userRepository->find($id);
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function getFindAll()
    {
        return $this->userRepository->findAll();
    }


    /**
     * @param User $user
     * @param bool $isNew
     * @return User
     */
    public function save(User $user, $isNew = false)
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

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @param User $user
     */
    public function remove(User $user)
    {
        $this->userRepository->remove($user);
    }


    /**
     * @param array $param
     * @return null|User
     */
    public function findOneBy($param)
    {
        return $this->userRepository->findOneBy($param);
    }

    /**
     * @param User $user
     * @return User
     */
    public function createUserPasswordToken(User $user)
    {
        $daysOfExpiration = 7;

        //generate token
        $generatedToken = $this->securityGenerator->generateSalt();

        //generate expiration date
        $expirationDate = new DateTime(sprintf('+ %d days', $daysOfExpiration));

        $user->setConfirmationToken($generatedToken);
        $user->setExpirationAt($expirationDate);

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @param string $token
     * @return mixed
     */
    public function findUnexpiredUserPasswordToken($token)
    {
        return $this->userRepository->findToken($token);
    }

    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAdminsAndSuperAdminsPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "asc")
    {
        $queryBuilder = $this->userRepository->getAdminsAndSuperAdminsAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }
}
