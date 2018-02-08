<?php
namespace BackendBundle\EventListener;

use BackendBundle\Entity\User;
use BackendBundle\Event\ForgotPasswordEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class ForgotPasswordListener
 */
class ForgotPasswordListener
{
    /**
     * @var null|ContainerInterface
     */
    private $container;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * ForgotPasswordListener constructor.
     * @param ContainerInterface|null $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param \Swift_Mailer $mailer
     */
    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param \Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param ForgotPasswordEvent $event
     */
    public function onForgotPassword(ForgotPasswordEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return; // do nothing
        }

        $data =  array(
            'firstName' => $user->getFirstName(),
            'email'     => $user->getEmail(),
            'url'       => $this->container->get('router')->generate('backend_reset_password', ['token' => $user->getConfirmationToken()], UrlGenerator::ABSOLUTE_URL),
            'signature' => $this->container->getParameter('signature'),
        );

        $from = array($this->container->getParameter('mailer_from_address') => $this->container->getParameter('mailer_from_name'));

        $template = $this->twig->loadTemplate('BackendBundle:Mail:forgotPassword.html.twig');
        $subject  = $template->renderBlock('subject', $data);
        $textBody = $template->renderBlock('body_text', $data);
        $htmlBody = $template->renderBlock('body_html', $data);

        $transport = \Swift_SmtpTransport::newInstance();

        $message = \Swift_Message::newInstance($transport)
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($user->getEmail());

        if (empty($htmlBody)) {
            $message->setBody($textBody);
        } else {
            $message
                ->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        }

        $this->mailer->send($message);
    }
}
