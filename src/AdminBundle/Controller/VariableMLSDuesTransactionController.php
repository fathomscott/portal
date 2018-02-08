<?php
namespace AdminBundle\Controller;

use AdminBundle\Controller\AbstractFilterableController;
use AdminBundle\Utils\ConvergeApi;
use AdminBundle\Utils\GlobalFilter;
use BackendBundle\Entity\Agent;
use BackendBundle\Entity\CreditCard;
use BackendBundle\Entity\District;
use BackendBundle\Entity\Transaction;
use BackendBundle\Event\MLSDuesAgentPaymentNotificationEvent;
use BackendBundle\Manager\TransactionManager;
use AdminBundle\Form\Type\Filter\TransactionFilterType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class VariableMLSDuesTransactionController
 * @Security("has_role('ROLE_ADMIN')")
 */
class VariableMLSDuesTransactionController extends AbstractFilterableController
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
     * @var ConvergeApi
     */
    private $convergeApi;

    /**
     * TransactionController constructor.
     * @param TransactionManager $transactionManager
     * @param GlobalFilter       $globalFilter
     * @param ConvergeApi        $convergeApi
     */
    public function __construct(TransactionManager $transactionManager, GlobalFilter $globalFilter, ConvergeApi $convergeApi)
    {
        $this->transactionManager = $transactionManager;
        $this->globalFilter = $globalFilter;
        $this->convergeApi = $convergeApi;
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

        $transactions = $this->transactionManager->getFindVariableMlsDuesPaginator($page, $filters);

        return $this->render('@Admin/VariableMLSDuesTransaction/index.html.twig', [
            'transactions' => $transactions,
            'total' => $transactions->getTotalItemCount(),
            'filterForm' => $this->getFilterFormView(),
        ]);
    }

    /**
     * @param Request     $request
     * @param Transaction $transaction
     * @return Response
     */
    public function chargeAction(Request $request, Transaction $transaction)
    {
        /** @var Agent $agent */
        $agent = $transaction->getUser();
        if (null === $terminalPin = $agent->getTerminalPin()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.no_terminal_pin');
        }
        if (null === $merchantId = $agent->getMerchantId()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.no_merchant_id');
        }

        $district = $transaction->getDistrict();
        $creditCard = $transaction->getUser()->getPaymentMethods()->first(); /** @var $creditCard CreditCard */

        if ($district === null ||
            !$creditCard instanceof CreditCard ||
            !$district->isMLSDuesRequired() ||
            $district->getMLSDuesType() !== District::MLS_DUES_TYPE_VARIABLE ||
            $transaction->getStatus() !== Transaction::STATUS_PENDING
        ) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder()
            ->add('amount', NumberType::class, ['constraints' => [new Range(['min' => 0.5, 'max' => 10000])]])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $terminalPin !== null) {
            $amount = $form->get('amount')->getData();
            $response = $this->convergeApi->request('ccsale', $terminalPin, $merchantId, [
                'ssl_token' => $creditCard->getCardToken(),
                'ssl_amount' => $amount,
            ]);

            if (array_key_exists('ssl_token_response', $response) && $response['ssl_token_response'] === 'SUCCESS') {
                $transaction->setStatus(Transaction::STATUS_APPROVED);
                $transaction->setAmount($amount);
                $transaction->setVendorId($response['ssl_txn_id']);
                $this->transactionManager->save($transaction);

                $this->get('event_dispatcher')->dispatch('backend_mls_dues_agent_payment_notification', new MLSDuesAgentPaymentNotificationEvent($transaction));
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('success.agent.charge', ['%amount%' => $amount]));

                return $this->redirectToRoute('admin_variable_mls_dues_transaction_index');

            } elseif (array_key_exists('errorMessage', $response)) {
                $this->get('session')->getFlashBag()->add('danger', $response['errorMessage']);
            } else {
                $this->get('session')->getFlashBag()->add('danger', 'Unknown error');
            }

            $this->get('event_dispatcher')->dispatch('backend_mls_dues_agent_payment_notification', new MLSDuesAgentPaymentNotificationEvent($transaction));
        }

        return $this->render('AdminBundle:VariableMLSDuesTransaction:manage.html.twig', [
            'form' => $form->createView(),
            'transaction' => $transaction,
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
        return 'admin.variable_mls_dues_transaction';
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->generateUrl('admin_variable_mls_dues_transaction_index');
    }
}
