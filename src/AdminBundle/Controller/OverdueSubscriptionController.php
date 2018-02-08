<?php
namespace AdminBundle\Controller;

use AdminBundle\Utils\GlobalFilter;
use BackendBundle\Manager\SubscriptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class OverdueSubscriptionController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class OverdueSubscriptionController extends Controller
{
    /**
     * @var SubscriptionManager
     */
    private $subscriptionManager;

    /**
     * @var GlobalFilter
     */
    private $globalFilter;

    /**
     * SubscriptionController constructor.
     * @param SubscriptionManager $subscriptionManager
     * @param GlobalFilter        $globalFilter
     */
    public function __construct(SubscriptionManager $subscriptionManager, GlobalFilter $globalFilter)
    {
        $this->subscriptionManager = $subscriptionManager;
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

        $subscriptions = $this->subscriptionManager->getFindOverduePaginator($page, $filters);

        return $this->render('@Admin/OverdueSubscription/index.html.twig', [
            'subscriptions' => $subscriptions,
            'total' => $subscriptions->getTotalItemCount(),
        ]);
    }
}
