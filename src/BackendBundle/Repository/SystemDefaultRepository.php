<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\SystemDefault;
use Doctrine\ORM\EntityRepository;

/**
 * SystemDefaultRepository
 */
class SystemDefaultRepository extends EntityRepository
{
    /**
     * @param SystemDefault $systemDefault
     */
    public function save(SystemDefault $systemDefault)
    {
        $this->_em->persist($systemDefault);
        $this->_em->flush($systemDefault);
    }

    /**
     * @return SystemDefault|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSystemDefault()
    {
        return $this->createQueryBuilder('a')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
