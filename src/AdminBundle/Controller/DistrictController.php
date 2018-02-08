<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\DistrictType;
use BackendBundle\Entity\District;
use BackendBundle\Manager\DistrictManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class DistrictController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class DistrictController extends Controller
{
    /**
     * @var DistrictManager
     */
    private $districtManager;

    /**
     * DistrictController constructor.
     * @param DistrictManager $districtManager
     */
    public function __construct(DistrictManager $districtManager)
    {
        $this->districtManager = $districtManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        $districts = $this->districtManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/District/index.html.twig', [
            'districts' => $districts,
            'total' => $districts->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request       $request
     * @param District|null $district
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, District $district = null)
    {
        if ($isNew = $district === null) {
            $district = new District();
        }

        $form = $this->createForm(DistrictType::class, $district);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var District $district */
            $district = $form->getData();

            if (!$district->isMLSDuesRequired()) {
                $district->setMLSDuesType(null);
                $district->setMLSFee(null);
            } elseif ($district->getMLSDuesType() === District::MLS_DUES_TYPE_VARIABLE) {
                $district->setMLSFee(null);
            }

            $this->districtManager->save($district);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.district.manage'));

            return $this->redirectToRoute('admin_district_edit', ['district' => $district->getId()]);
        }

        return $this->render('AdminBundle:District:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'district' => $district,
        ]);
    }

    /**
     * @param District $district
     * @return RedirectResponse
     */
    public function deleteAction(District $district)
    {
        $this->districtManager->remove($district);

        $this->get('session')->getFlashBag()->add('success', 'success.district.delete');

        return $this->redirectToRoute('admin_district_index');
    }
}
