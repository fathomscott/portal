<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\SystemMessage;
use BackendBundle\Entity\User;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * SystemMessageRepository
 */
class SystemMessageRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param SystemMessage $systemMessage
     */
    public function save(SystemMessage $systemMessage)
    {
        $this->_em->persist($systemMessage);
        $this->_em->flush();
    }

    /**
     * @param SystemMessage $systemMessage
     */
    public function remove(SystemMessage $systemMessage)
    {
        $this->_em->remove($systemMessage);
        $this->_em->flush();
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a');

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $filters
     * @return QueryBuilder
     */
    public function applyFilters(QueryBuilder $queryBuilder, $filters)
    {
        /* TODO applying filters */

        return $queryBuilder;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUnreadMessages(User $user)
    {
        $stmt = $this->_em->getConnection()->prepare(sprintf(
            'SELECT dismissed_system_messages.system_message_id FROM dismissed_system_messages WHERE dismissed_system_messages.user_id = %s',
            $user->getId()
        ));
        $stmt->execute();
        $dismissedSystemMessageIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        return $this->createQueryBuilder('a')
            ->where('a.id NOT IN (:ids)')
            ->andWhere('(a.startDate <= :now OR a.startDate IS NULL)')
            ->andWhere('(a.endDate >= :now OR a.endDate IS NULL)')
            ->setParameters([
                'ids' => $dismissedSystemMessageIds,
                'now' => new \DateTime(date('Y-m-d')),
            ])
            ->orderBy('a.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User          $user
     * @param SystemMessage $systemMessage
     * @return null|SystemMessage
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function messageAlreadyDismissed(User $user, SystemMessage $systemMessage)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.users', 'b')
            ->where('a.id = :system_message')
            ->andWhere('b.id = :user')
            ->setParameters([
                'user' => $user,
                'system_message' => $systemMessage,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
