<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\User;
use BackendBundle\User\AccountTypeOptions;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * @param User $user
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
    }

    /**
     * @param User $user
     */
    public function remove(User $user)
    {
        $this->_em->remove($user);
        $this->_em->flush($user);
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b')
            ->leftJoin('a.subscription', 'b')
            ;

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    public function getAdminsAndSuperAdminsAllQueryBuilder()
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b')
            ->leftJoin('a.subscription', 'b')
            ->where('a.accountType = :super_admin')
            ->orWhere('a.accountType = :admin')
            ->setParameters([
                'super_admin' => AccountTypeOptions::SUPER_ADMIN,
                'admin' => AccountTypeOptions::ADMIN,
            ])
        ;

        return $qb;
    }

    /**
     * @param string $username
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u')
            ->where('u.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
        ;

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf('Unable to find an active User object identified by "%s".', $username);

            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     * @return null|object
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);

        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $class)
            );
        }

        return $this->find($user->getId());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    /**
     * @param string $token
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findToken($token)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.confirmationToken = :token');
        $qb->andWhere('u.expirationAt > CURRENT_DATE()');
        $qb->setParameter('token', $token);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
