<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\Filter\GlobalFilterType;
use BackendBundle\Entity\SystemMessage;
use BackendBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $systemMessages = $this->get('backend.system_message_manager')->getUnreadMessages($user);

        return $this->render('@Admin/Default/index.html.twig', ['systemMessages' => $systemMessages]);
    }

    /**
     * @param SystemMessage $systemMessage
     * @param Request       $request
     * @return Response
     */
    public function dismissMessageAction(SystemMessage $systemMessage, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();
        $systemMessageManager = $this->get('backend.system_message_manager');

        if ($systemMessageManager->messageAlreadyDismissed($user, $systemMessage)) {
            return new Response('error');
        }

        $systemMessageManager->dismissMessage($user, $systemMessage);

        return new Response('success');
    }

    /**
     * @return Response
     */
    public function globalFilerAction()
    {
        $form = $this->createForm(GlobalFilterType::class);

        return $this->render('@Admin/helpers/global_filter.html.twig', ['form' => $form->createView()]);
    }
}
