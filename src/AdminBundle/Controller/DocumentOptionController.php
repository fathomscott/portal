<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\DocumentOptionType;
use BackendBundle\Entity\DocumentOption;
use BackendBundle\Manager\DocumentOptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class DocumentOptionController
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class DocumentOptionController extends Controller
{
    /**
     * @var DocumentOptionManager
     */
    private $documentOptionManager;

    /**
     * DocumentOptionController constructor.
     * @param DocumentOptionManager $documentOptionManager
     */
    public function __construct(DocumentOptionManager $documentOptionManager)
    {
        $this->documentOptionManager = $documentOptionManager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);

        $filters = [];

        $documentOptions = $this->documentOptionManager->getFindAllPaginator($page, $filters);

        return $this->render('@Admin/DocumentOption/index.html.twig', [
            'documentOptions' => $documentOptions,
            'total' => $documentOptions->getTotalItemCount(),
        ]);
    }

    /**
     * @param Request             $request
     * @param DocumentOption|null $documentOption
     * @return RedirectResponse|Response
     */
    public function manageAction(Request $request, DocumentOption $documentOption = null)
    {
        $isNew = $documentOption === null;

        $form = $this->createForm(DocumentOptionType::class, $documentOption);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentOption = $form->getData();
            $this->documentOptionManager->save($documentOption);
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.document_option.manage'));

            return $this->redirectToRoute('admin_document_option_edit', ['documentOption' => $documentOption->getId()]);
        }

        return $this->render('AdminBundle:DocumentOption:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'documentOption' => $documentOption,
        ]);
    }

    /**
     * @param DocumentOption $documentOption
     * @return RedirectResponse
     */
    public function deleteAction(DocumentOption $documentOption)
    {
        $this->documentOptionManager->remove($documentOption);

        $this->get('session')->getFlashBag()->add('success', 'success.document_option.delete');

        return $this->redirectToRoute('admin_document_option_index');
    }
}
