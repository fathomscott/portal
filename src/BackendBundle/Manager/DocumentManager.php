<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Document;
use BackendBundle\Repository\DocumentRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class DocumentManager
 */
class DocumentManager
{
    /**
     * @var DocumentRepository
     */
    private $documentRepository;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @param DocumentRepository $documentRepository
     * @param Paginator $paginator
     */
    public function __construct(
        DocumentRepository $documentRepository,
        Paginator $paginator
    )
    {
        $this->documentRepository = $documentRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|Document
     */
    public function find($id)
    {
        return $this->documentRepository->find($id);
    }

    /**
     * @return Document[]
     */
    public function getFindAll()
    {
        return $this->documentRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|Document
     */
    public function findOneBy($param)
    {
        return $this->documentRepository->findOneBy($param);
    }

    /**
     * @param Document $document
     * @return Document
     */
    public function save(Document $document)
    {
        $this->documentRepository->save($document);

        return $document;
    }

    /**
     * @param Document $document
     */
    public function remove(Document $document)
    {
        $this->documentRepository->remove($document);
    }


    /**
     * @param integer $page
     * @param null $filters
     * @param integer $limit
     * @param string $sortFieldName
     * @param string $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->documentRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param integer $page
     * @param null $filters
     * @param integer $limit
     * @param string $sortFieldName
     * @param string $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllPendingPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->documentRepository->getFindAllPendingQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param integer $page
     * @param null $filters
     * @param integer $limit
     * @param string $sortFieldName
     * @param string $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllExpiringPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.expirationDate", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->documentRepository->getFindAllExpiringQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param integer $page
     * @param null $filters
     * @param integer $limit
     * @param string $sortFieldName
     * @param string $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllExpiredPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.expirationDate", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->documentRepository->getFindAllExpiredQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param Agent $agent
     * @param integer $page
     * @param null $filters
     * @param integer $limit
     * @param string $sortFieldName
     * @param string $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllByAgentPaginator($agent, $page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->documentRepository->getFindAllByAgentQueryBuilder($agent, $filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param Agent $agent
     * @return array
     */
    public function getLatestFilesForAgent(Agent $agent)
    {
        return $this->documentRepository->getLatestFilesForAgent($agent);
    }

    /**
     * Set expired status for all expired documents
     */
    public function setExpiredStatus()
    {
        $this->documentRepository->setExpiredStatus();
    }
}
