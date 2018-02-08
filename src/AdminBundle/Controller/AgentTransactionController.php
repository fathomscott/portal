<?php
namespace AdminBundle\Controller;

use AdminBundle\Controller\AbstractFilterableController;
use AdminBundle\Form\Type\Filter\AgentTransactionFilterType;
use BackendBundle\Entity\Agent;
use BackendBundle\Manager\TransactionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AgentTransactionController
 * @Security("has_role('ROLE_AGENT')")
 */
class AgentTransactionController extends AbstractFilterableController
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * TransactionController constructor.
     * @param TransactionManager $transactionManager
     */
    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = $this->getFilters();

        /** @var Agent $agent */
        $agent = $this->getUser();

        $transactions = $this->transactionManager->getFindAllByAgentPaginator($agent, $page, $filters);

        return $this->render('@Admin/AgentTransaction/index.html.twig', [
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
        return AgentTransactionFilterType::class;
    }

    /**
     * @return string
     */
    public function getFilterFormName()
    {
        return 'agent.transaction';
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->generateUrl('admin_agent_transaction_index');
    }
}
