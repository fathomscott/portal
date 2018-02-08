<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\StateType;
use BackendBundle\Entity\State;
use BackendBundle\Manager\StateManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class StateController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class StateController extends Controller
{
    /**
     * @var StateManager
     */
    private $stateManager;

    /**
     * StateController constructor.
     * @param StateManager $stateManager
     */
    public function __construct(StateManager $stateManager)
    {
        $this->stateManager = $stateManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        $states = $this->stateManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/State/index.html.twig', [
            'states' => $states,
            'total' => $states->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request    $request
     * @param State|null $state
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, State $state = null)
    {
        $isNew = $state === null;

        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $state = $form->getData();
            $this->stateManager->save($state);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.state.manage'));

            return $this->redirectToRoute('admin_state_edit', ['state' => $state->getId()]);
        }

        return $this->render('AdminBundle:State:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'state' => $state,
        ]);
    }

    /**
     * @param State $state
     * @return RedirectResponse
     */
    public function deleteAction(State $state)
    {
        $this->stateManager->remove($state);

        $this->get('session')->getFlashBag()->add('success', 'success.state.delete');

        return $this->redirectToRoute('admin_state_index');
    }
}
