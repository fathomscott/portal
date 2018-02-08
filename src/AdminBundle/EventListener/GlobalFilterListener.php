<?php
namespace AdminBundle\EventListener;


use AdminBundle\Utils\GlobalFilter;
use BackendBundle\Manager\DistrictManager;
use BackendBundle\Manager\StateManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class GlobalFilterListener
 */
class GlobalFilterListener
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var DistrictManager
     */
    private $districtManager;

    /**
     * @var StateManager
     */
    private $stateManager;

    /**
     * GlobalFilterListener constructor.
     * @param SessionInterface              $session
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param DistrictManager               $districtManager
     * @param StateManager                  $stateManager
     */
    public function __construct(
        SessionInterface $session,
        AuthorizationCheckerInterface $authorizationChecker,
        DistrictManager $districtManager,
        StateManager $stateManager
    ) {
        $this->session = $session;
        $this->districtManager = $districtManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->stateManager = $stateManager;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->request->has('global_filter_reset')) {
            $this->session->remove(GlobalFilter::STATE);
            $this->session->remove(GlobalFilter::DISTRICT);

            return;
        }

        $stateId = $request->get(GlobalFilter::STATE);
        $districtId = $request->get(GlobalFilter::DISTRICT);
        if (($districtId || $stateId) && $this->authorizationChecker->isGranted("ROLE_SUPER_ADMIN")) {
            $state = $this->stateManager->find($stateId);
            if (null !== $state) {
                $this->session->set(GlobalFilter::STATE, $stateId);

                return;
            }

            $district = $this->districtManager->find($districtId);
            if (null !== $district) {
                $this->session->set(GlobalFilter::DISTRICT, $districtId);

                return;
            }
        }
    }
}
