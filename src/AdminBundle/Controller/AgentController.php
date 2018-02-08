<?php
namespace AdminBundle\Controller;

use AdminBundle\Event\AgentInvitationEvent;
use AdminBundle\Form\Type\ChangePasswordType;
use AdminBundle\Form\Type\AgentType;
use AdminBundle\Form\Type\CreditCardType;
use AdminBundle\Form\Type\Filter\AgentFilterType;
use AdminBundle\Form\Type\SubscriptionType;
use AdminBundle\Utils\ConvergeApi;
use AdminBundle\Utils\GlobalFilter;
use BackendBundle\Entity\Agent;
use BackendBundle\Entity\BillingOption;
use BackendBundle\Entity\CreditCard;
use BackendBundle\Entity\District;
use BackendBundle\Entity\Subscription;
use BackendBundle\Entity\Transaction;
use BackendBundle\Manager\AgentDistrictManager;
use BackendBundle\Manager\AgentManager;
use BackendBundle\Manager\CreditCardManager;
use BackendBundle\Manager\DocumentOptionManager;
use BackendBundle\Manager\SubscriptionManager;
use BackendBundle\Manager\TransactionManager;
use BackendBundle\Manager\UserManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class AgentController
 */
class AgentController extends AbstractFilterableController
{
    /**
     * @var AgentManager
     */
    private $agentManager;

    /**
     * @var SubscriptionManager
     */
    private $subscriptionManager;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var CreditCardManager
     */
    private $creditCardManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var AgentDistrictManager
     */
    private $agentDistrictManager;

    /**
     * @var DocumentOptionManager
     */
    private $documentOptionManager;

    /**
     * @var GlobalFilter
     */
    private $globalFilter;

    /**
     * @var ConvergeApi
     */
    private $convergeApi;

    /**
     * AgentController constructor.
     * @param AgentManager          $agentManager
     * @param SubscriptionManager   $subscriptionManager
     * @param TransactionManager    $transactionManager
     * @param CreditCardManager     $creditCardManager
     * @param UserManager           $userManager
     * @param AgentDistrictManager  $agentDistrictManager
     * @param DocumentOptionManager $documentOptionManager
     * @param GlobalFilter          $globalFilter
     * @param ConvergeApi           $convergeApi
     */
    public function __construct(
        AgentManager $agentManager,
        SubscriptionManager $subscriptionManager,
        TransactionManager $transactionManager,
        CreditCardManager $creditCardManager,
        UserManager $userManager,
        AgentDistrictManager $agentDistrictManager,
        DocumentOptionManager $documentOptionManager,
        GlobalFilter $globalFilter,
        ConvergeApi $convergeApi
    ) {
        $this->agentManager = $agentManager;
        $this->subscriptionManager = $subscriptionManager;
        $this->transactionManager = $transactionManager;
        $this->creditCardManager = $creditCardManager;
        $this->userManager = $userManager;
        $this->agentDistrictManager = $agentDistrictManager;
        $this->documentOptionManager = $documentOptionManager;
        $this->globalFilter = $globalFilter;
        $this->convergeApi = $convergeApi;
    }

    /**
     * @return mixed
     */
    public function getFilterForm()
    {
        return AgentFilterType::class;
    }

    /**
     * @return string
     */
    public function getFilterFormName()
    {
        return 'admin.agent';
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->generateUrl('admin_agent_index');
    }

    /**
     * @param Request $request
     * @return Response
     * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_AGENT') and user.isDistrictDirector())")
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = $this->globalFilter->getFilters();
        $filters = array_merge($filters, $this->getFilters());

        $agents = $this->agentManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/Agent/index.html.twig', [
            'agents' => $agents,
            'total' => $agents->getTotalItemCount(),
            'filterForm' => $this->getFilterFormView(),
        ]);
    }

    /**
     * @param Agent $agent
     * @return Response
     */
    public function viewAction(Agent $agent)
    {
        $this->denyAccessUnlessGranted('view', $agent);

        return $this->render('@Admin/Agent/view.html.twig', ['agent' => $agent]);
    }

    /**
     * @param Request $request
     * @param Agent   $agent
     * @return Response
     */
    public function transactionAction(Request $request, Agent $agent)
    {
        $this->denyAccessUnlessGranted('view', $agent);

        $page = $request->query->get('page', 1);

        $transactions = $this->transactionManager->getFindAllByAgentPaginator($agent, $page, []);

        return $this->render('AdminBundle:Agent:transactions.html.twig', [
            'agent' => $agent,
            'transactions' => $transactions,
            'total' => $transactions->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request    $request
     * @param Agent|null $agent
     * @return RedirectResponse|Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function manageAction(Request $request, Agent $agent = null)
    {
        $isNew = $agent === null;
        $agent = $agent !== null ? $agent : new Agent();

        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Agent $agent */
            $agent = $form->getData();

	    // Make sure the email is unique...
	    $existingUser = $this->agentManager->findOneBy(["email" => $agent->getEmail()]);
	    $existingAgent = $this->agentManager->findOneBy(["personalEmail" => $agent->getEmail()]);
	    $existingUser2 = $this->agentManager->findOneBy(["email" => $agent->getPersonalEmail()]);
	    $existingAgent2 = $this->agentManager->findOneBy(["personalEmail" => $agent->getPersonalEmail()]);
	    $name = '';
	    $email = '';
	    if (!is_null($existingAgent) and ($existingAgent->getId() != $agent->getId())) {
		$email = $existingAgent->getEmail();
		$name = $existingAgent->getFirstName().' '.$existingAgent->getLastName();
	    }
	    elseif (!is_null($existingUser) and ($existingUser->getId() != $agent->getId())) {
		$email = $existingUser->getEmail();
		$name = $existingUser->getFirstName().' '.$existingUser->getLastName();
	    }
	    elseif (!is_null($existingAgent2) and ($existingAgent2->getId() != $agent->getId())) {
		$email = $existingAgent2->getEmail();
		$name = $existingAgent2->getFirstName().' '.$existingAgent2->getLastName();
	    }
	    elseif (!is_null($existingUser2) and ($existingUser2->getId() != $agent->getId())) {
		$email = $existingUser2->getEmail();
		$name = $existingUser2->getFirstName().' '.$existingUser2->getLastName();
	    }
	    if ($name != '') {
		$this->get('session')->getFlashBag()->set('danger', $this->get('translator')->trans('error.agent.manage.email_exists',["%name%" => $name, "%email%" => $email]));
		return $isNew ? $this->redirectToRoute('admin_agent_index') : $this->redirect($this->generateUrl('admin_agent_edit', ['agent' => $agent->getId()]));
	    }

            else {
		if ($isNew) {
               		$this->documentOptionManager->createRequiredDocumentsForAgent($agent);
               		$this->transactionManager->createDistrictPendingTransactionsForAgent($agent);
               		$agent->setPlainPassword(md5('Fathomletmein'));
            	}

            	$this->agentManager->save($agent, $isNew);
            	$this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.agent.manage'));

                return $this->redirect($this->generateUrl('admin_agent_edit', ['agent' => $agent->getId()]));
	    }

        }

        return $this->render('AdminBundle:Agent:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'agent' => $agent,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Agent $agent
     * @return RedirectResponse
     */
    public function deleteAction(Agent $agent)
    {
        $this->agentManager->remove($agent);

        $this->get('session')->getFlashBag()->add('success', 'success.agent.delete');

        return $this->redirectToRoute('admin_agent_index');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param Agent   $agent
     * @return RedirectResponse|Response
     */
    public function changePasswordAction(Request $request, Agent $agent)
    {
        $form = $this->createForm(ChangePasswordType::class, $agent);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->agentManager->save($form->getData());
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.agent_change_password.manage'));

            return $this->redirect($this->generateUrl('admin_agent_view', ['agent' => $agent->getId()]));
        }

        return $this->render('@Admin/Agent/changePassword.html.twig', [
            'form' => $form->createView(),
            'agent' => $agent,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param Agent   $agent
     * @return RedirectResponse|Response
     */
    public function subscriptionAction(Request $request, Agent $agent)
    {
        $subscription = $agent->getSubscription();

        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Subscription $subscription */
            $subscription = $form->getData();
            $subscription->setUser($agent);

            $this->subscriptionManager->save($subscription);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.subscription.manage'));

            return $this->redirect($this->generateUrl('admin_agent_view', [
                'agent' => $agent->getId(),
            ]));
        }

        return $this->render('AdminBundle:Agent:subscription.html.twig', [
            'form' => $form->createView(),
            'agent' => $agent,
            'subscription' => $subscription,
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param Agent   $agent
     * @return RedirectResponse
     */
    public function sendActivationEmailAction(Request $request, Agent $agent)
    {
        $agent = $this->userManager->createUserPasswordToken($agent);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('admin_agent_invitation_sent', new AgentInvitationEvent($agent));

        $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.agent.activation'));

        return $this->redirect($this->generateUrl('admin_agent_index'));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param Agent   $agent
     * @return RedirectResponse|Response
     */
    public function chargeAction(Request $request, Agent $agent)
    {
        if (count($agent->getPaymentMethods()) === 0) {
            throw $this->createNotFoundException();
        }

        if (null === $terminalPin = $agent->getTerminalPin()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.no_terminal_pin');
            return $this->redirectToRoute('admin_agent_view', ['agent' => $agent->getId()]);
        }

        if (null === $merchantId = $agent->getMerchantId()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.no_merchant_id');
            return $this->redirectToRoute('admin_agent_view', ['agent' => $agent->getId()]);
        }

        $form = $this->createFormBuilder()
            ->add('amount', NumberType::class, ['constraints' => [new Range(['min' => 0.05, 'max' => 10000])]])
            ->add('billingOption', EntityType::class, ['class' => BillingOption::class])
	    ->add('description', TextType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $amount = $form->get('amount')->getData();
	    $description = $form->get('description')->getData();
            /** @var CreditCard $creditCard */
            $creditCard = $agent->getPaymentMethods()->first();
	    $token = $creditCard->getCardToken();

	    // Try to update the description first...
            $response = $this->convergeApi->request('ccupdatetoken', $terminalPin, $merchantId, [
                'ssl_token' => $token,
	        'ssl_description' => $description,
            ]);

	    // Now try to charge them...
            $response = $this->convergeApi->request('ccsale', $terminalPin, $merchantId, [
                'ssl_token' => $token,
	        'ssl_description' => $description,
                'ssl_amount' => $amount,
            ]);

            if (array_key_exists('ssl_result_message', $response) && $response['ssl_result_message'] == 'APPROVAL') {
                $billingOption = $form->get('billingOption')->getData();
                $transaction = new Transaction();
                $transaction->setUser($agent);
                $transaction->setAmount($amount);
                $transaction->setPaymentMethod($creditCard);
                $transaction->setBillingOption($billingOption);
                $transaction->setStatus(Transaction::STATUS_APPROVED);
                $transaction->setVendorId($response['ssl_txn_id']);
                $transaction->setNotes(sprintf('Manual charge (%s) by %s', $description, $this->getUser()));
                $this->transactionManager->save($transaction);

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('success.agent.charge', ['%amount%' => $amount]));
            } elseif (array_key_exists('ssl_result_message', $response)) {
                $this->get('session')->getFlashBag()->add('danger', $response['ssl_result_message']);
            } elseif (array_key_exists('errorMessage', $response)) {
                $this->get('session')->getFlashBag()->add('danger', $response['errorMessage']);
            } else {
                $this->get('session')->getFlashBag()->add('danger', 'error.unknown_error');
            }

            return $this->redirectToRoute('admin_agent_charge', ['agent' => $agent->getId()]);
        }

        return $this->render('@Admin/Agent/charge.html.twig', [
            'agent' => $agent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Agent       $agent
     * @param Transaction $transaction
     * @return RedirectResponse
     */
    public function refundAction(Agent $agent, Transaction $transaction)
    {
        if ($transaction->getStatus() !== Transaction::STATUS_APPROVED) {
            throw $this->createNotFoundException();
        }

        if (null === $terminalPin = $agent->getTerminalPin()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.no_terminal_pin');
            return $this->redirectToRoute('admin_agent_view', ['agent' => $agent->getId()]);
        }

        if (null === $merchantId = $agent->getMerchantId()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.no_merchant_id');
            return $this->redirectToRoute('admin_agent_view', ['agent' => $agent->getId()]);
        }

        $response = $this->convergeApi->request('ccreturn', $terminalPin, $merchantId, [
            'ssl_txn_id' => $transaction->getVendorId(),
        ]);

        if (array_key_exists('ssl_result_message', $response) && $response['ssl_result_message'] == 'APPROVAL') {
            $transaction->setStatus(Transaction::STATUS_REFUNDED);
            $this->transactionManager->save($transaction);

            $this->get('session')->getFlashBag()->add('success', 'success.transaction.refund');
        } elseif (array_key_exists('ssl_result_message', $response)) {
            $this->get('session')->getFlashBag()->add('danger', $response['ssl_result_message']);
        } elseif (array_key_exists('errorMessage', $response)) {
            $this->get('session')->getFlashBag()->add('danger', $response['errorMessage']);
        } else {
            $this->get('session')->getFlashBag()->add('danger', 'error.unknown_error');
        }

        return $this->redirectToRoute('admin_agent_transactions', ['agent' => $agent->getId()]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param Agent   $agent
     * @return RedirectResponse|Response
     */
    public function paymentMethodAction(Request $request, Agent $agent)
    {
        if (null === $terminalPin = $agent->getTerminalPin()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.no_terminal_pin');
            return $this->redirectToRoute('admin_agent_view', ['agent' => $agent->getId()]);
        }

        if (null === $merchantId = $agent->getMerchantId()) {
            $this->get('session')->getFlashBag()->add('danger', 'error.payment_method.no_merchant_id');
            return $this->redirectToRoute('admin_agent_view', ['agent' => $agent->getId()]);
        }

        $creditCard = $this->creditCardManager->findOneBy(['user' => $agent]);

        $creditCardForm = $this->createForm(CreditCardType::class, $creditCard);
        $creditCardForm->handleRequest($request);
        if ($creditCardForm->isSubmitted() && $creditCardForm->isValid()) {
            /** @var CreditCard $creditCard */
            $creditCard = $creditCardForm->getData();
            $creditCard->setUser($agent);

            if ($oldToken = $creditCard->getCardToken()) {
                $this->convergeApi->request('ccdeletetoken', $terminalPin, $merchantId, [
                    'ssl_token' => $oldToken,
                ]);
            }

            $response = $this->convergeApi->request('ccgettoken', $terminalPin, $merchantId, [
                'ssl_card_number' => $creditCard->getCardNumber(),
                'ssl_exp_date' => $creditCard->getExpMonth().$creditCard->getExpYear(),
                'ssl_cvv2cvc2' => $creditCard->getCvv(),
                'ssl_first_name' => $creditCard->getFirstName(),
                'ssl_last_name' => $creditCard->getLastName(),
                'ssl_email' => $agent->getEmail(),
                'ssl_add_token' => 'Y',
            ]);

            if (array_key_exists('ssl_token_response', $response) && $response['ssl_token_response'] === 'SUCCESS') {
                $creditCard->setLastFour(substr($creditCard->getCardNumber(), -4));
                $creditCard->setCardToken($response['ssl_token']);
                $this->creditCardManager->save($creditCard);
                $this->get('session')->getFlashBag()->add('success', 'success.credit_card.manage');

                return $this->redirectToRoute('admin_agent_payment_method', ['agent' => $agent->getId()]);
            } elseif (array_key_exists('errorMessage', $response)) {
                $this->get('session')->getFlashBag()->add('danger', $response['errorMessage']);
            } else {
                $this->get('session')->getFlashBag()->add('danger', 'error.credit_card.none');
            }
        } elseif (null !== $creditCard) {
            $this->get('session')->getFlashBag()->add('info', $this->get('translator')->trans('success.credit_card.agent_on_file', ['%lastFour%' => $creditCard->getLastFour()]));
        }

        return $this->render('@Admin/Agent/paymentMethod.html.twig', [
            'agent' => $agent,
            'creditCard' => $creditCard,
            'form' => $creditCardForm->createView(),
        ]);
    }
}
