<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\StaffType;
use BackendBundle\Entity\Admin;
use BackendBundle\Entity\User;
use BackendBundle\Manager\AdminManager;
use BackendBundle\Manager\SuperAdminManager;
use BackendBundle\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class StaffController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class StaffController extends Controller
{
    /**
     * @var AdminManager
     */
    private $adminManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * StaffController constructor.
     * @param AdminManager $adminManager
     * @param UserManager  $userManager
     */
    public function __construct(AdminManager $adminManager, UserManager $userManager)
    {
        $this->adminManager = $adminManager;
        $this->userManager = $userManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        $staffs = $this->userManager->getFindAdminsAndSuperAdminsPaginator($page, $filters);

        return $this->render('@Admin/Staff/index.html.twig', [
            'staffs' => $staffs,
            'total' => $staffs->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request   $request
     * @param User|null $staff
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, User $staff = null)
    {
        $isNew = $staff === null;
        if (!$isNew && !$staff instanceof Admin) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(StaffType::class, $staff, ['require_password' => $isNew]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $staff = $form->getData();
            $this->adminManager->save($staff, $isNew);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.staff.manage'));

            return $this->redirectToRoute('admin_staff_edit', ['staff' => $staff->getId()]);
        }

        return $this->render('AdminBundle:Staff:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'staff' => $staff,
        ]);
    }

    /**
     * @param Admin $staff
     * @return RedirectResponse
     */
    public function deleteAction(Admin $staff)
    {
        if ($this->getUser()->getId() === $staff->getId()) {
            throw $this->createNotFoundException();
        }

        $this->adminManager->remove($staff);

        $this->get('session')->getFlashBag()->add('success', 'success.staff.delete');

        return $this->redirectToRoute('admin_staff_index');
    }
}
