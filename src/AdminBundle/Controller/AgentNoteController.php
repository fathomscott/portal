<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\AgentNoteType;
use BackendBundle\Entity\Agent;
use BackendBundle\Entity\AgentNote;
use BackendBundle\Manager\AgentNoteManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AgentNoteController
 * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_AGENT') and user.isDistrictDirector())")
 */
class AgentNoteController extends Controller
{
    /**
     * @var AgentNoteManager
     */
    private $agentNoteManager;

    /**
     * AgentNoteController constructor.
     * @param AgentNoteManager $agentNoteManager
     */
    public function __construct(AgentNoteManager $agentNoteManager)
    {
        $this->agentNoteManager = $agentNoteManager;
    }

    /**
     * @param Request $request
     * @param Agent   $agent
     * @return Response
     */
    public function indexAction(Request $request, Agent $agent)
    {
        $this->denyAccessUnlessGranted('view', $agent);

        $page = $request->query->get('page', 1);

        $filters = [];

        $agentNotes = $this->agentNoteManager->getFindAllByAgentPaginator($agent, $this->getUser(), $page, $filters);

        return $this->render('@Admin/AgentNote/index.html.twig', [
            'agentNotes' => $agentNotes,
            'total' => $agentNotes->getTotalItemCount(),
            'agent' => $agent,
        ]);
    }

    /**
     * @param Request $request
     * @param Agent   $agent
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request, Agent $agent)
    {
        $this->denyAccessUnlessGranted('view', $agent);

        return $this->manageAction($request, $agent);
    }

    /**
     * @param Request        $request
     * @param Agent          $agent
     * @param AgentNote|null $agentNote
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, Agent $agent, AgentNote $agentNote = null)
    {
        $isNew = $agentNote === null;

        if (!$isNew) {
            $this->denyAccessUnlessGranted('view', $agentNote);
        }

        if ($isNew) {
            $agentNote = new AgentNote();
            $agentNote->setAgent($agent);
            $agentNote->setAuthor($this->getUser());
        }

        $form = $this->createForm(AgentNoteType::class, $agentNote);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->agentNoteManager->save($form->getData());
            $this->get('session')->getFlashBag()->set('success', 'success.agent_note.manage');

            return $this->redirectToRoute('admin_agent_note_edit', ['agentNote' => $agentNote->getId(), 'agent' => $agent->getId()]);
        }

        return $this->render('AdminBundle:AgentNote:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'agentNote' => $agentNote,
            'agent' => $agent,
        ]);
    }

    /**
     * @param Agent     $agent
     * @param AgentNote $agentNote
     * @return RedirectResponse
     */
    public function deleteAction(Agent $agent, AgentNote $agentNote)
    {
        $this->denyAccessUnlessGranted('delete', $agentNote);

        $this->agentNoteManager->remove($agentNote);

        $this->get('session')->getFlashBag()->add('success', 'success.agent_note.delete');

        return $this->redirectToRoute('admin_agent_note_index', ['agent' => $agent->getId()]);
    }
}
