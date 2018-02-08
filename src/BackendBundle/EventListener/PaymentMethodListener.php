<?php
namespace BackendBundle\EventListener;

use BackendBundle\Entity\User;
use BackendBundle\Manager\UserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PaymentMethodListener
 * @package BackendBundle\EventListener
 */
class PaymentMethodListener implements EventSubscriberInterface
{
    const SESSION_NAME = 'PAYMENT_METHOD_LISTENER_VERIFIED';

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * PaymentMethodListener constructor.
     * @param UserManager                   $userManager
     * @param UrlGeneratorInterface         $urlGenerator
     * @param SessionInterface              $session
     * @param ContainerInterface            $container
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param TranslatorInterface           $translator
     */
    public function __construct(
        UserManager $userManager,
        UrlGeneratorInterface $urlGenerator,
        SessionInterface $session,
        ContainerInterface $container,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        TranslatorInterface $translator
    ) {
        $this->userManager = $userManager;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->container = $container;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws NotFoundHttpException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        /* check if payment method is already set */
        if ($this->session->get(self::SESSION_NAME, false)) {
            return;
        }

        // do not bother processing this listener for anonymous users
        if (empty($this->tokenStorage->getToken()) || !$this->authorizationChecker->isGranted('ROLE_USER')) {
            return;
        }

        // do not bother processing this listener for anyone except agents
        if (!$this->authorizationChecker->isGranted('ROLE_AGENT')) {
            $this->session->set(self::SESSION_NAME, true);

            return;
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        // do not block users from entering a payment method or from agreeing to the terms of service
        $request = $event->getRequest();
        if ($request->get('_route') === 'admin_agent_profile_payment_method_manage') {
            return;
        }

        // if no payment methods are added, set flash bag and send to payment method route
        if (count($user->getPaymentMethods()) === 0) {
            $this->session->getFlashBag()->set('danger', $this->translator->trans('error.payment_method.agent'));
            $response = new RedirectResponse($this->urlGenerator->generate('admin_agent_profile_payment_method_manage'));
            $event->setResponse($response);
            $event->stopPropagation();

            return;
        }

        $this->session->set(self::SESSION_NAME, true);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 31)),
        );
    }
}
