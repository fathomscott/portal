<?php
namespace BackendBundle\Controller;

use BackendBundle\Event\ForgotPasswordEvent;
use BackendBundle\Event\ResetPasswordEvent;
use BackendBundle\Form\Type\PasswordType;
use BackendBundle\Form\Utils\FormErrorsHelper;
use BackendBundle\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SecurityController
 */
class SecurityController extends Controller
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * SecurityController constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function loginAction(Request $request)
    {
        if ($this->getUser() && (true === $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))) {
            return $this->redirect($this->generateUrl('backend_logout'));
        }

        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $exception = $authenticationUtils->getLastAuthenticationError();


        if ($exception && method_exists($exception, 'getMessage')) {
            $this->get('session')->getFlashBag()->add('danger', $exception->getMessage());
        }

        return $this->render('@Backend/Security/login.html.twig');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function forgotPasswordAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $email = $request->get('email');
            $user = $this->userManager->findOneBy(['email' => $email]);

            if ($user) {
                $user = $this->userManager->createUserPasswordToken($user);

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('backend_forgot_password', new ForgotPasswordEvent($user));

                $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.password.reset_request'));

                return $this->redirect($this->generateUrl('backend_login'));
            }

            $error = $this->get('translator')->trans('error.user.email_not_exists');
            $this->get('session')->getFlashBag()->add('danger', $error);
        }

        return $this->render('BackendBundle:Security:forgotPassword.html.twig');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws NotFoundHttpException
     */
    public function resetPasswordAction(Request $request)
    {
        $token = $request->get('token');

        if (!$user = $this->userManager->findUnexpiredUserPasswordToken($token)) {
            throw new NotFoundHttpException($this->get('translator')->trans('error.user.token_not_exists'));
        }

        $form = $this->createForm(PasswordType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $securityGenerator = $this->get('backend.security_generator');
                $formData = $request->get('password');
                $password = $formData['password']['first'];
                $salt = $securityGenerator->generateSalt();
                $user->setSalt($salt);
                $user->setConfirmationToken(null);
                $user->setPassword($securityGenerator->encodePassword($password, $salt));
                $this->userManager->save($user);

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('backend_reset_password', new ResetPasswordEvent($user));

                $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans('success.password.password_reset'));

                return $this->redirect($this->generateUrl('backend_login'));
            } else {
                $errors = FormErrorsHelper::getErrorsAsArray($form->get('password')->get('first'));
                $this->get('session')->getFlashBag()->set('danger', implode("\n", $errors));
            }
        }

        return $this->render('BackendBundle:Security:resetPassword.html.twig', ['token' => $token, 'form' => $form->createView()]);
    }
}
