<?php
namespace AdminBundle\Controller;

use BackendBundle\Entity\Email;
use BackendBundle\Manager\EmailManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class EmailController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class EmailController extends Controller
{
    /**
     * @var EmailManager
     */
    private $emailManager;

    /**
     * EmailController constructor.
     * @param EmailManager $emailManager
     */
    public function __construct(EmailManager $emailManager)
    {
        $this->emailManager = $emailManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        $emails = $this->emailManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/Email/index.html.twig', [
            'emails' => $emails,
            'total' => $emails->getTotalItemCount(),
        ]);
    }

    /**
     * @param Email $email
     * @return Response
     */
    public function viewAction(Email $email)
    {
        return $this->render('@Admin/Email/view.html.twig', ['email' => $email]);
    }

    /**
     * @param Email $email
     * @return RedirectResponse
     */
    public function deleteAction(Email $email)
    {
        $this->emailManager->remove($email);

        $this->get('session')->getFlashBag()->add('success', 'success.email.delete');

        return $this->redirectToRoute('admin_email_index');
    }
}
