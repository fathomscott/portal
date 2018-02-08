<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\BillingOptionType;
use BackendBundle\Entity\BillingOption;
use BackendBundle\Manager\BillingOptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class BillingOptionController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class BillingOptionController extends Controller
{
    /**
     * @var BillingOptionManager
     */
    private $billingOptionManager;

    /**
     * BillingOptionController constructor.
     * @param BillingOptionManager $billingOptionManager
     */
    public function __construct(BillingOptionManager $billingOptionManager)
    {
        $this->billingOptionManager = $billingOptionManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        $billingOptions = $this->billingOptionManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/BillingOption/index.html.twig', [
            'billingOptions' => $billingOptions,
            'total' => $billingOptions->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request            $request
     * @param BillingOption|null $billingOption
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, BillingOption $billingOption = null)
    {
        $isNew = $billingOption === null;

        $form = $this->createForm(BillingOptionType::class, $billingOption);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $billingOption = $form->getData();
            $this->billingOptionManager->save($billingOption);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.billing_option.manage'));

            return $this->redirectToRoute('admin_billing_option_edit', ['billingOption' => $billingOption->getId()]);
        }

        return $this->render('AdminBundle:BillingOption:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'billingOption' => $billingOption,
        ]);
    }

    /**
     * @param BillingOption $billingOption
     * @return RedirectResponse
     */
    public function deleteAction(BillingOption $billingOption)
    {
        $this->billingOptionManager->remove($billingOption);

        $this->get('session')->getFlashBag()->add('success', 'success.billing_option.delete');

        return $this->redirectToRoute('admin_billing_option_index');
    }
}
