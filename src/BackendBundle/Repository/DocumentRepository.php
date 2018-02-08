<?php
namespace BackendBundle\Repository;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Document;
use BackendBundle\Repository\Behaviors\FilterBehaviorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * DocumentRepository
 */
class DocumentRepository extends EntityRepository implements FilterBehaviorInterface
{
    /**
     * @param Document $document
     */
    public function save(Document $document)
    {
        $this->_em->persist($document);
        $this->_em->flush($document);
    }

    /**
     * @param Document $document
     */
    public function remove(Document $document)
    {
	$document->setStatus(99);
        //$this->_em->remove($document);
        $this->_em->flush($document);
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
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllPendingQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b, c, z')
            ->leftJoin('a.agent', 'b')
            ->leftJoin('a.documentOption', 'c')
            ->leftJoin('b.subscription', 'z')
            ->where('a.status = :status')
            ->setParameter('status', Document::STATUS_PENDING)
            ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllExpiringQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b, c, z')
            ->leftJoin('a.agent', 'b')
            ->leftJoin('a.documentOption', 'c')
            ->leftJoin('b.subscription', 'z')
            ->where('a.expirationDate >= :today')
            ->andWhere('a.expirationDate <= :next_month')
            ->setParameters([
                'next_month' => new \DateTime('+30 days'),
                'today' => new \DateTime('today'),
            ])
        ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param null $filters
     * @return QueryBuilder
     */
    public function getFindAllExpiredQueryBuilder($filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b, c, z')
            ->leftJoin('a.agent', 'b')
            ->leftJoin('a.documentOption', 'c')
            ->leftJoin('b.subscription', 'z')
            ->where('a.expirationDate < :today')
            ->setParameter('today', new \DateTime('today'))
        ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param Agent $agent
     * @param null  $filters
     * @return QueryBuilder
     */
    public function getFindAllByAgentQueryBuilder($agent, $filters = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, b, c')
            ->leftJoin('a.agent', 'b')
            ->leftJoin('a.documentOption', 'c')
            ->where('a.agent = :agent')
            ->setParameter('agent', $agent)
            ;

        if ($filters !== null) {
            $qb = $this->applyFilters($qb, $filters);
        }

        return $qb;
    }

    /**
     * @param Agent $agent
     * @return array
     */
    public function getLatestFilesForAgent(Agent $agent)
    {
        /* get last version of documents for each option */
        $stmt = $this->_em->getConnection()->prepare(sprintf('
            SELECT d.id
            FROM (SELECT * FROM document WHERE agent_id = %d ORDER BY uploaded_date DESC) AS d
            GROUP BY d.document_option_id', $agent->getId()));

        $stmt->execute();
        $ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        return $this->createQueryBuilder('a')
            ->where('a.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * set expired status for all expired documents
     */
    public function setExpiredStatus()
    {
        $this->_em->createQueryBuilder()
            ->update($this->_entityName, 'a')
            ->set('a.status', Document::STATUS_EXPIRED)
            ->where('a.expirationDate < :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->execute();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $filters
     * @return QueryBuilder
     */
    public function applyFilters(QueryBuilder $queryBuilder, $filters)
    {
        if (isset($filters['district']) && !empty($filters['district'])) {
            $queryBuilder
                ->leftJoin('b.agentDistricts', 'd')
                ->leftJoin('d.district', 'e')
                ->andWhere('e.id IN (:district_ids)')
                ->setParameter('district_ids', $filters['district']);
        }

        if (isset($filters['state']) && !empty($filters['state'])) {
            $queryBuilder
                ->leftJoin('b.agentDistricts', 'd')
                ->leftJoin('d.district', 'e')
                ->leftJoin('e.state', 'f')
                ->andWhere('f.id = :state_id')
                ->setParameter('state_id', $filters['state']);
        }

        if (isset($filters['firstName']) && !empty($filters['firstName'])) {
            $queryBuilder
                ->andWhere('b.firstName LIKE :first_name')
                ->setParameter('first_name', '%'.addcslashes($filters['firstName'], '_%').'%');
        }

        if (isset($filters['lastName']) && !empty($filters['lastName'])) {
            $queryBuilder
                ->andWhere('b.lastName LIKE :last_name')
                ->setParameter('last_name', '%'.addcslashes($filters['lastName'], '_%').'%');
        }

        $filterBehavior = new Behaviors\FilterBehavior();

        return $filterBehavior
            ->setEqualsColumns(['documentOption'])
            ->applyFilters($queryBuilder, $filters)
            ;
    }
}
