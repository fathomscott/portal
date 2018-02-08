<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\LicenseCategoryType;
use BackendBundle\Entity\LicenseCategory;
use BackendBundle\Manager\LicenseCategoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class LicenseCategoryController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class LicenseCategoryController extends Controller
{
    /**
     * @var LicenseCategoryManager
     */
    private $licenseCategoryManager;

    /**
     * LicenseCategoryController constructor.
     * @param LicenseCategoryManager $licenseCategoryManager
     */
    public function __construct(LicenseCategoryManager $licenseCategoryManager)
    {
        $this->licenseCategoryManager = $licenseCategoryManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        $licenseCategories = $this->licenseCategoryManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/LicenseCategory/index.html.twig', [
            'licenseCategories' => $licenseCategories,
            'total' => $licenseCategories->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request              $request
     * @param LicenseCategory|null $licenseCategory
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, LicenseCategory $licenseCategory = null)
    {
        $isNew = $licenseCategory === null;

        $form = $this->createForm(LicenseCategoryType::class, $licenseCategory);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $licenseCategory = $form->getData();
            $this->licenseCategoryManager->save($licenseCategory);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.license_category.manage'));

            return $this->redirectToRoute('admin_license_category_edit', ['licenseCategory' => $licenseCategory->getId()]);
        }

        return $this->render('AdminBundle:LicenseCategory:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'licenseCategory' => $licenseCategory,
        ]);
    }

    /**
     * @param LicenseCategory $licenseCategory
     * @return RedirectResponse
     */
    public function deleteAction(LicenseCategory $licenseCategory)
    {
        $this->licenseCategoryManager->remove($licenseCategory);

        $this->get('session')->getFlashBag()->add('success', 'success.license_category.delete');

        return $this->redirectToRoute('admin_license_category_index');
    }
}
