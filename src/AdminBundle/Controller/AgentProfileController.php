<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\AgentPlanType;
use AdminBundle\Form\Type\AgentProfileType;
use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Plan;
use BackendBundle\Entity\Subscription;
use BackendBundle\Manager\AgentManager;
use AdminBundle\Form\Type\AgentChangePasswordType;
use BackendBundle\Manager\SubscriptionManager;
use BackendBundle\Manager\PlanManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

/**
 * Class AgentProfileController
 * @Security("has_role('ROLE_AGENT')")
 */
class AgentProfileController extends Controller
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
     * @var PlanManager
     */
    private $planManager;

    /**
     * StateController constructor.
     * @param AgentManager        $agentManager
     * @param SubscriptionManager $subscriptionManager
     * @param PlanManager $planManager
     */
    public function __construct(
        AgentManager $agentManager,
        SubscriptionManager $subscriptionManager,
        PlanManager $planManager
    ) {
        $this->agentManager = $agentManager;
        $this->subscriptionManager = $subscriptionManager;
        $this->planManager = $planManager;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function profileAction(Request $request)
    {
        /** @var Agent $agent */
        $agent = $this->getUser();

        $form = $this->createForm(AgentProfileType::class, $agent);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $agent = $form->getData();
            $this->agentManager->save($agent);

            /* crop head shot image */
            $path = sprintf(
                '%s/../web%s',
                $this->get('kernel')->getRootDir(),
                $this->get('vich_uploader.storage')->resolveUri($agent, 'headShotFile')
            );

            $cropData = $form->get('cropImageData')->getData();
            $cropData = empty($cropData) ? null : json_decode($cropData, true);
            if (!is_null($cropData) && !is_null($agent->getHeadShotName())) {
                $imagine = new Imagine();
                $image = $imagine->open($path);

                $startPoint = new Point(
                    intval($cropData['x']),
                    intval($cropData['y'])
                );
                $box = new Box(
                    intval($cropData['width']) * intval($cropData['scaleX']),
                    intval($cropData['height']) * intval($cropData['scaleY'])
                );
                $image
                    ->crop($startPoint, $box)
                    ->save($path)
                ;
            }

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.state.manage'));

            return $this->redirectToRoute('admin_agent_profile');
        }

        return $this->render('@Admin/AgentProfile/profile.html.twig', [
            'form' => $form->createView(),
            'agent' => $agent,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function changePasswordAction(Request $request)
    {
        $agent = $this->getUser();

        $form = $this->createForm(AgentChangePasswordType::class, $agent);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->agentManager->save($form->getData());
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.change_password.manage'));

            return $this->redirect($this->generateUrl('admin_agent_profile_change_password'));
        }

        return $this->render('@Admin/AgentProfile/changePassword.html.twig', [
            'form' => $form->createView(),
            'agent' => $agent,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function planAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];
        
        /** @var Plans $plans */
        $plans = $this->planManager->getFindActivePublicPaginator($page, $filters);
        
        /** @var Plan $currentPlan */
        $currentPlan = $this->getUser()->getSubscription()->getPlan();

        return $this->render('@Admin/AgentProfile/plan.html.twig', [
            'plans' => $plans,
            'currentPlan'  => $currentPlan,
            'total' => $plans->getTotalItemCount(),
        ]);
    }
}
