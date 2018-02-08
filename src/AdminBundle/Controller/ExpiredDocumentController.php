<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\Filter\DocumentFilterType;
use AdminBundle\Utils\GlobalFilter;
use BackendBundle\Manager\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class ExpiredDocumentController
 * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_AGENT') and user.isDistrictDirector())")
 */
class ExpiredDocumentController extends AbstractFilterableController
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var GlobalFilter
     */
    private $globalFilter;

    /**
     * PendingDocumentController constructor.
     * @param DocumentManager $documentManager
     * @param GlobalFilter    $globalFilter
     */
    public function __construct(DocumentManager $documentManager, GlobalFilter $globalFilter)
    {
        $this->documentManager = $documentManager;
        $this->globalFilter = $globalFilter;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = $this->globalFilter->getFilters();
        $filters = array_merge($filters, $this->getFilters());

        $documents = $this->documentManager->getFindAllExpiredPaginator($page, $filters);

        return $this->render('@Admin/ExpiredDocument/index.html.twig', [
            'documents' => $documents,
            'total' => $documents->getTotalItemCount(),
            'filterForm' => $this->getFilterFormView(),
        ]);
    }

    /**
     * @return mixed
     */
    public function getFilterForm()
    {
        return DocumentFilterType::class;
    }

    /**
     * @return string
     */
    public function getFilterFormName()
    {
        return 'admin.expired_documents';
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->generateUrl('admin_expired_document_index');
    }
}
