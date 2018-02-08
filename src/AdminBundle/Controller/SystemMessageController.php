<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\SystemMessageType;
use BackendBundle\Entity\SystemMessage;
use BackendBundle\Manager\SystemMessageManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class SystemMessageController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class SystemMessageController extends Controller
{
    /**
     * @var SystemMessageManager
     */
    private $systemMessageManager;

    /**
     * SystemMessageController constructor.
     * @param SystemMessageManager $systemMessageManager
     */
    public function __construct(SystemMessageManager $systemMessageManager)
    {
        $this->systemMessageManager = $systemMessageManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        $systemMessages = $this->systemMessageManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/SystemMessage/index.html.twig', [
            'systemMessages' => $systemMessages,
            'total' => $systemMessages->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request            $request
     * @param SystemMessage|null $systemMessage
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, SystemMessage $systemMessage = null)
    {
        $isNew = $systemMessage === null;

        $form = $this->createForm(SystemMessageType::class, $systemMessage);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $systemMessage = $form->getData();
            $this->systemMessageManager->save($systemMessage);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.system_message.manage'));

            return $this->redirectToRoute('admin_system_message_edit', ['systemMessage' => $systemMessage->getId()]);
        }

        return $this->render('AdminBundle:SystemMessage:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'systemMessage' => $systemMessage,
        ]);
    }

    /**
     * @param SystemMessage $systemMessage
     * @return RedirectResponse
     */
    public function deleteAction(SystemMessage $systemMessage)
    {
        $this->systemMessageManager->remove($systemMessage);

        $this->get('session')->getFlashBag()->add('success', 'success.system_message.delete');

        return $this->redirectToRoute('admin_system_message_index');
    }
}
