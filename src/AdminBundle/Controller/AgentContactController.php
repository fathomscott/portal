<?php
namespace AdminBundle\Controller;

use BackendBundle\Entity\Agent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AgentContactController
 */
class AgentContactController extends Controller
{
    public function __construct()
    {

    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('@Admin/AgentContact/index.html.twig');
    }
}
