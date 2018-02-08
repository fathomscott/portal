<?php
namespace AdminBundle\Controller;

use AdminBundle\Event\InviteTeamMemberEvent;
use AdminBundle\Form\Type\TeamMemberType;
use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Team;
use BackendBundle\Entity\TeamMember;
use BackendBundle\Manager\AgentManager;
use BackendBundle\Manager\TeamMemberManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AgentTeamMemberController
 * @Security("has_role('ROLE_AGENT')")
 */
class AgentTeamMemberController extends Controller
{
    /**
     * @var TeamMemberManager
     */
    private $teamMemberManager;

    /**
     * @var AgentManager
     */
    private $agentManager;

    /**
     * AgentTeamMemberController constructor.
     * @param TeamMemberManager $teamMemberManager
     * @param AgentManager      $agentManager
     */
    public function __construct(TeamMemberManager $teamMemberManager, AgentManager $agentManager)
    {
        $this->teamMemberManager = $teamMemberManager;
        $this->agentManager = $agentManager;
    }

    /**
     * @param Request $request
     * @param Team    $team
     * @return Response
     */
    public function indexAction(Request $request, Team $team)
    {
        /** @var Agent $agent */
        $agent = $this->getUser();

        if (!$team->isTeamMember($agent)) {
            throw $this->createNotFoundException();
        }

        $page = $request->query->get('page', 1);

        $filters = [];

        $teamMembers = $this->teamMemberManager->getFindAllByTeamPaginator($team, $page, $filters);

        return $this->render('@Admin/AgentTeamMember/index.html.twig', [
            'team' => $team,
            'teamMembers' => $teamMembers,
            'isTeamLeader' => $team->isTeamLeader($agent),
            'total' => $teamMembers->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request $request
     * @param Team    $team
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, Team $team)
    {
        /** @var Agent $agent */
        $agent = $this->getUser();

        if (!$team->isTeamLeader($agent)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(TeamMemberType::class);
        $form->get('message')->setData($this->get('translator')->trans('labels.team_member_invitation', ['%name%' => $agent->getFullName()]));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $token = bin2hex(random_bytes(18));
            /** @var TeamMember $teamMember */
            $teamMember = $form->getData();
            $teamMember->setTeam($team);
            $teamMember->setToken($token);

            $agent = $this->agentManager->findOneBy(['email' => $teamMember->getInvitationEmail()]);
            $teamMember->setAgent($agent);

            $this->teamMemberManager->save($teamMember);

            $event = new InviteTeamMemberEvent($teamMember, $form->get('message')->getData());
            $this->get('event_dispatcher')->dispatch('admin_team_member_invitation', $event);

            $this->get('session')->getFlashBag()->set('success', 'success.team_member.invite');

            return $this->redirectToRoute('admin_agent_team_member_index', ['team' => $team->getId()]);
        }

        return $this->render('AdminBundle:AgentTeamMember:manage.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param string  $token
     * @return RedirectResponse
     */
    public function acceptInvitationAction(Request $request, $token)
    {
        $teamMember = $this->teamMemberManager->findOneBy(['token' => $token]);

        $agent = $this->getUser();
        if ($teamMember === null) {
            throw $this->createNotFoundException();
        }

        $teamMember->setAgent($agent);
        $teamMember->setToken(null);
        $this->teamMemberManager->save($teamMember);

        return $this->redirectToRoute('admin_agent_team_member_index', ['team' => $teamMember->getTeam()->getId()]);
    }

    /**
     * @param Team       $team
     * @param TeamMember $teamMember
     * @return RedirectResponse
     */
    public function deleteAction(Team $team, TeamMember $teamMember)
    {
        if (!$team->isTeamLeader($this->getUser())) {
            throw $this->createNotFoundException();
        }

        $this->teamMemberManager->remove($teamMember);

        $this->get('session')->getFlashBag()->add('success', 'success.team_member.delete');

        return $this->redirectToRoute('admin_agent_team_member_index', ['team' => $team->getId()]);
    }
}
