<?php
namespace AdminBundle\Controller;

use BackendBundle\Entity\Agent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AgentTermsController
 */
class AgentTermsController extends Controller
{
    public function __construct()
    {

    }

    /**
     * @param Request $request
     * @return Response
     */
    public function privacyPolicyAction(Request $request)
    {
        return $this->render('@Admin/AgentTerms/privacyPolicy.html.twig');
    }
    
    /**
     * @param Request $request
     * @return Response
     */
    public function refundPolicyAction(Request $request)
    {
        return $this->render('@Admin/AgentTerms/refundPolicy.html.twig');
    }
}
