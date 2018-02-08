<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\SystemDefaultType;
use BackendBundle\Entity\SystemDefault;
use BackendBundle\Manager\SystemDefaultManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class SystemDefaultController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class SystemDefaultController extends Controller
{
    /**
     * @var SystemDefaultManager
     */
    private $systemDefaultManager;

    /**
     * SystemDefaultController constructor.
     * @param SystemDefaultManager $systemDefaultManager
     */
    public function __construct(SystemDefaultManager $systemDefaultManager)
    {
        $this->systemDefaultManager = $systemDefaultManager;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request)
    {
        $systemDefault = $this->systemDefaultManager->getSystemDefault();

        $form = $this->createForm(SystemDefaultType::class, $systemDefault);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $systemDefault = $form->getData();
            $this->systemDefaultManager->save($systemDefault);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.system_default.manage'));
        }

        return $this->render('AdminBundle:SystemDefault:manage.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
