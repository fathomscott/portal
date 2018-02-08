<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Document;
use BackendBundle\Entity\DocumentOption;
use BackendBundle\Repository\DocumentOptionRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;

/**
 * Class DocumentOptionManager
 */
class DocumentOptionManager
{
    /**
     * @var DocumentOptionRepository
     */
    private $documentOptionRepository;

    /**
     * @var Paginator
     */
    private $paginator;

     /**
     * @param DocumentOptionRepository $documentOptionRepository
     * @param Paginator                $paginator
     */
    public function __construct(
        DocumentOptionRepository $documentOptionRepository,
        Paginator $paginator
    ) {
        $this->documentOptionRepository = $documentOptionRepository;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $id
     * @return null|DocumentOption
     */
    public function find($id)
    {
        return $this->documentOptionRepository->find($id);
    }

    /**
     * @return DocumentOption[]
     */
    public function getFindAll()
    {
        return $this->documentOptionRepository->findAll();
    }

    /**
     * @param array $param
     * @return null|DocumentOption
     */
    public function findOneBy($param)
    {
        return $this->documentOptionRepository->findOneBy($param);
    }

    /**
     * @param DocumentOption $documentOption
     * @return DocumentOption
     */
    public function save(DocumentOption $documentOption)
    {
        $this->documentOptionRepository->save($documentOption);

        return $documentOption;
    }

    /**
     * @param DocumentOption $documentOption
     */
    public function remove(DocumentOption $documentOption)
    {
        $this->documentOptionRepository->remove($documentOption);
    }


    /**
     * @param integer $page
     * @param null    $filters
     * @param integer $limit
     * @param string  $sortFieldName
     * @param string  $sortFieldDirection
     * @return SlidingPagination
     */
    public function getFindAllPaginator($page, $filters = null, $limit = 50, $sortFieldName = "a.id", $sortFieldDirection = "desc")
    {
        $queryBuilder = $this->documentOptionRepository->getFindAllQueryBuilder($filters);

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'defaultSortFieldName' => $sortFieldName,
            'defaultSortDirection' => $sortFieldDirection,
        ]);
    }

    /**
     * @param Agent $agent
     * @return Agent
     */
    public function createRequiredDocumentsForAgent(Agent $agent)
    {
        $requiredDocumentOptions = $this->documentOptionRepository->findBy(['required' => true]);
        foreach ($requiredDocumentOptions as $documentOption) {
            $document = new Document();
            $document->setAgent($agent);
            $document->setDocumentOption($documentOption);

            $agent->addDocument($document);
        }

        return $agent;
    }
}
