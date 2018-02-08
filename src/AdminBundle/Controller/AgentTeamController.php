<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\TeamType;
use BackendBundle\Entity\Team;
use BackendBundle\Entity\TeamMember;
use BackendBundle\Manager\TeamManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AgentTeamController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class AgentTeamController extends Controller
{
    /**
     * @var TeamManager
     */
    private $teamManager;

    /**
     * AgentTeamController constructor.
     * @param TeamManager $teamManager
     */
    public function __construct(TeamManager $teamManager)
    {
        $this->teamManager = $teamManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        //$agent = $this->getUser();

        //$teams = $this->teamManager->getFindAllByAgentPaginator($agent, $page, $filters);
        $teams = $this->teamManager->getFindAllByPaginator($page, $filters);

        return $this->render('@Admin/AgentTeam/index.html.twig', [
            'teams' => $teams,
            'total' => $teams->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request   $request
     * @param Team|null $team
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, Team $team = null)
    {
        if ($team && !$team->isTeamLeader($this->getUser())) {
            throw $this->createNotFoundException();
        }

        $isNew = $team === null;

        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Team $team */
            $team = $form->getData();

            if ($isNew) {
                $teamMember = new TeamMember();
                $teamMember->setAgent($this->getUser());
                $teamMember->setTeamLeader(true);
                $teamMember->setTeam($team);
                $team->addMember($teamMember);
            }

            $this->teamManager->save($team);
            $this->get('session')->getFlashBag()->set('success', 'success.team.manage');

            return $this->redirectToRoute('admin_agent_team_edit', ['team' => $team->getId()]);
        }

        return $this->render('AdminBundle:AgentTeam:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'team' => $team,
        ]);
    }

    /**
     * @param Team $team
     * @return RedirectResponse
     */
    public function deleteAction(Team $team)
    {
        if (!$team->isTeamLeader($this->getUser())) {
            throw $this->createNotFoundException();
        }

        $this->teamManager->remove($team);

        $this->get('session')->getFlashBag()->add('success', 'success.team.delete');

        return $this->redirectToRoute('admin_agent_team_index');
    }
}
