<?php
namespace AdminBundle\Controller;

use AdminBundle\Utils\GlobalFilter;
use BackendBundle\Entity\Agent;
use BackendBundle\Entity\User;
use BackendBundle\Manager\ReferralManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class ReferralController
 * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or user.isDistrictDirector()")
 */
class ReferralController extends Controller
{
    /**
     * @var ReferralManager
     */
    private $referralManager;

    /**
     * @var GlobalFilter
     */
    private $globalFilter;

    /**
     * ReferralController constructor.
     * @param ReferralManager $referralManager
     * @param GlobalFilter    $globalFilter
     */
    public function __construct(ReferralManager $referralManager, GlobalFilter $globalFilter)
    {
        $this->referralManager = $referralManager;
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

        $referrals = $this->referralManager->countReferralsPaginator($page, $filters);

        $total = $this->referralManager->countReferringUsers();
        $referrals->setTotalItemCount($total);

        return $this->render('@Admin/Referral/index.html.twig', [
            'referrals' => $referrals,
            'total' => $total,
        ]);
    }

    /**
     * @param Request $request
     * @param User    $user
     * @return Response
     */
    public function referralList(Request $request, User $user)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createAccessDeniedException();
        }

        $referralUsers = $this->referralManager->getReferralList($user);

        return $this->render('@Admin/Referral/referralList.html.twig', [
            'user' => $user,
            'referralUsers' => $referralUsers,
        ]);
    }
}
