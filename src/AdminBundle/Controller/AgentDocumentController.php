<?php
namespace AdminBundle\Controller;

use AdminBundle\Form\Type\DocumentType;
use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Document;
use BackendBundle\Manager\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class AgentDocumentController
 * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_AGENT') and user.isDistrictDirector())")
 */
class AgentDocumentController extends Controller
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var DownloadHandler
     */
    private $downloadHandler;

    /**
     * DocumentController constructor.
     * @param DocumentManager $documentManager
     * @param DownloadHandler $downloadHandler
     */
    public function __construct(DocumentManager $documentManager, DownloadHandler $downloadHandler)
    {
        $this->documentManager = $documentManager;
        $this->downloadHandler = $downloadHandler;
    }

    /**
     * @param Request $request
     * @param Agent   $agent
     * @return Response
     */
    public function indexAction(Request $request, Agent $agent)
    {
        $this->denyAccessUnlessGranted('view', $agent);

        $page = $request->query->get('page', 1);

        $filters = [];
        $documents = $this->documentManager->getFindAllByAgentPaginator($agent, $page, $filters);

        return $this->render('@Admin/AgentDocument/index.html.twig', [
            'documents' => $documents,
            'total' => $documents->getTotalItemCount(),
            'agent' => $agent,
        ]);
    }

    /**
     * @param Request $request
     * @param Agent   $agent
     * @return RedirectResponse|Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAction(Request $request, Agent $agent)
    {
        return $this->manageAction($request, $agent);
    }

    /**
     * @param Request       $request
     * @param Agent         $agent
     * @param Document|null $document
     * @return RedirectResponse|Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function manageAction(Request $request, Agent $agent, Document $document = null)
    {
        $isNew = $document === null;

        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Document $document */
            $document = $form->getData();
            $document->setStatus(Document::STATUS_VALID);
            $document->setAgent($agent);
            $this->documentManager->save($document);
            $this->get('session')->getFlashBag()->set('success', 'success.agent_document.manage');
            return $this->redirectToRoute('admin_agent_document_index', ['agent' => $agent->getId()]);
        }

        return $this->render('AdminBundle:AgentDocument:manage.html.twig', [
            'isNew' => $isNew,
            'form' => $form->createView(),
            'document' => $document,
            'agent' => $agent,
        ]);
    }

    /**
     * @param Agent    $agent
     * @param Document $document
     * @return RedirectResponse
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Agent $agent, Document $document)
    {
        $this->documentManager->remove($document);

        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('success.agent_document.delete', ['%docNumber%' => $document->getId()]));

        return $this->redirectToRoute('admin_agent_document_index', ['agent' => $agent->getId()]);
    }

    /**
     * @param Agent    $agent
     * @param Document $document
     * @return RedirectResponse
     */
    public function downloadAction(Agent $agent, Document $document)
    {
        $this->denyAccessUnlessGranted('view', $agent);

        $ext = strrchr($document->getDocumentName(), '.');

        return $this->downloadHandler->downloadObject($document, 'documentFile', null, $document->getDocumentOption()->getName().$ext);
    }
}
