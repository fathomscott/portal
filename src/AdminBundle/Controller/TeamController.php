<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\TeamType;
use AdminBundle\Utils\GlobalFilter;
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
 * @Security("has_role('ROLE_SUPER_ADMIN') or (has_role('ROLE_AGENT') and user.isDistrictDirector())")
 */
class TeamController extends Controller
{
    /**
     * @var TeamManager
     */
    private $teamManager;

    /**
     * @var GlobalFilter
     */
    private $globalFilter;

    /**
     * AgentTeamController constructor.
     * @param TeamManager  $teamManager
     * @param GlobalFilter $globalFilter
     */
    public function __construct(TeamManager $teamManager, GlobalFilter $globalFilter)
    {
        $this->teamManager = $teamManager;
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

        $teams = $this->teamManager->getFindAllByPaginator($page, $filters);

        return $this->render('@Admin/Team/index.html.twig', [
            'teams' => $teams,
            'total' => $teams->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request $request
     * @param Team    $team
     * @return Response
     */
    public function viewAction(Request $request, Team $team)
    {
        $this->denyAccessUnlessGranted('view', $team);

        return $this->render('@Admin/Team/view.html.twig', [
            'team' => $team,
        ]);
    }
}
