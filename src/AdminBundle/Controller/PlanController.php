<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\PlanType;
use BackendBundle\Entity\Plan;
use BackendBundle\Manager\PlanManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class PlanController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class PlanController extends Controller
{
    /**
     * @var PlanManager
     */
    private $planManager;

    /**
     * PlanController constructor.
     * @param PlanManager $planManager
     */
    public function __construct(PlanManager $planManager)
    {
        $this->planManager = $planManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        $plans = $this->planManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/Plan/index.html.twig', [
            'plans' => $plans,
            'total' => $plans->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request   $request
     * @param Plan|null $plan
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, Plan $plan = null)
    {
        $isNew = $plan === null;

        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $plan = $form->getData();
            $this->planManager->save($plan);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.plan.manage'));

            return $this->redirectToRoute('admin_plan_edit', ['plan' => $plan->getId()]);
        }

        return $this->render('AdminBundle:Plan:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'plan' => $plan,
        ]);
    }

    /**
     * @param Plan $plan
     * @return RedirectResponse
     */
    public function deleteAction(Plan $plan)
    {
        $this->planManager->remove($plan);

        $this->get('session')->getFlashBag()->add('success', 'success.plan.delete');

        return $this->redirectToRoute('admin_plan_index');
    }
}
