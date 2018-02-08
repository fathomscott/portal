<?php
namespace AdminBundle\Controller;

use AdminBundle\Controller\AbstractFilterableController;
use AdminBundle\Utils\GlobalFilter;
use BackendBundle\Manager\TransactionManager;
use AdminBundle\Form\Type\Filter\TransactionFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class TransactionController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class TransactionController extends AbstractFilterableController
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var GlobalFilter
     */
    private $globalFilter;

    /**
     * TransactionController constructor.
     * @param TransactionManager $transactionManager
     * @param GlobalFilter       $globalFilter
     */
    public function __construct(TransactionManager $transactionManager, GlobalFilter $globalFilter)
    {
        $this->transactionManager = $transactionManager;
        $this->globalFilter = $globalFilter;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $globalFilters = $this->globalFilter->getFilters();
        $filters = array_merge($globalFilters, $this->getFilters());
	//echo "<pre>FILTERS:".print_r($this->getFilters(),true)."</pre>";

        $transactions = $this->transactionManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/Transaction/index.html.twig', [
            'transactions' => $transactions,
            'total' => $transactions->getTotalItemCount(),
            'filterForm' => $this->getFilterFormView(),
        ]);
    }

    /**
     * @return mixed
     */
    public function getFilterForm()
    {
        return TransactionFilterType::class;
    }

    /**
     * @return string
     */
    public function getFilterFormName()
    {
        return 'admin.transaction';
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->generateUrl('admin_transaction_index');
    }
}
