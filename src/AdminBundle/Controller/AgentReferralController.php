<?php
namespace AdminBundle\Controller;

use AdminBundle\Controller\AbstractFilterableController;
use AdminBundle\Form\Type\Filter\AgentReferralFilterType;
use BackendBundle\Entity\Agent;
use BackendBundle\Manager\ReferralManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AgentReferralController
 * @Security("has_role('ROLE_AGENT')")
 */
class AgentReferralController extends AbstractFilterableController
{
    /**
     * @var ReferralManager
     */
    private $referralManager;

    /**
     * ReferralController constructor.
     * @param ReferralManager $referralManager
     */
    public function __construct(ReferralManager $referralManager)
    {
        $this->referralManager = $referralManager;
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

        $referrals = $this->referralManager->getFindByUserPaginator($agent, $page, $filters);

        return $this->render('@Admin/AgentReferral/index.html.twig', [
            'referrals' => $referrals,
            'total' => $referrals->getTotalItemCount(),
            'filterForm' => $this->getFilterFormView(),
        ]);
    }

    /**
     * @return mixed
     */
    public function getFilterForm()
    {
        return AgentReferralFilterType::class;
    }

    /**
     * @return string
     */
    public function getFilterFormName()
    {
        return 'agent.referral';
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->generateUrl('admin_agent_referral_index');
    }
}
