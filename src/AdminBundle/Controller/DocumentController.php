<?php
namespace AdminBundle\Controller;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\Document;
use BackendBundle\Manager\DocumentManager;
use BackendBundle\Manager\SystemDefaultManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class DocumentController
 * @Security("has_role('ROLE_AGENT')")
 */
class DocumentController extends Controller
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var SystemDefaultManager
     */
    private $systemDefaultManager;

    /**
     * @var DownloadHandler
     */
    private $downloadHandler;

    /**
     * DocumentController constructor.
     * @param DocumentManager      $documentManager
     * @param SystemDefaultManager $systemDefaultManager
     * @param DownloadHandler      $downloadHandler
     */
    public function __construct(DocumentManager $documentManager, SystemDefaultManager $systemDefaultManager, DownloadHandler $downloadHandler)
    {
        $this->documentManager = $documentManager;
        $this->systemDefaultManager = $systemDefaultManager;
        $this->downloadHandler = $downloadHandler;
    }

    /**
     * @return Response
     */
    public function indexAction()
    {
        $agent = $this->getUser();

        $documents = $this->documentManager->getLatestFilesForAgent($agent);
        $expiringDocuments = [];
        foreach ($documents as $document) { /** @var $document Document */
            if ($document->getExpirationDate() !== null && ($document->getExpirationDate()->getTimestamp() - time()) < 2592000) { /* 30 days */
                $expiringDocuments[] = $document->getDocumentOption()->getName();
            }
        }

        return $this->render('@Admin/Document/index.html.twig', [
            'documents' => $documents,
            'total' => count($documents),
            'agent' => $agent,
            'expiringDocuments' => $expiringDocuments,
            'onboardingEmail' => $this->systemDefaultManager->getSystemDefault()->getOnboardingEmail(),
        ]);
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
