<?php
namespace AdminBundle\EventListener;

use AdminBundle\Event\AgentInvitationEvent;
use BackendBundle\Entity\Agent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class AgentInvitationListener
 */
class AgentInvitationListener
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
     * @param AgentInvitationEvent $event
     */
    public function onInvitationSent(AgentInvitationEvent $event)
    {
        $agent = $event->getAgent();

        if (!$agent instanceof Agent) {
            return; // do nothing
        }

        $data =  array(
            'firstName' => $agent->getFirstName(),
            'email'     => $agent->getEmail(),
            'url'       => $this->container->get('router')->generate('backend_reset_password', ['token' => $agent->getConfirmationToken()], UrlGenerator::ABSOLUTE_URL),
            'signature' => $this->container->getParameter('signature'),
        );

        $from = array($this->container->getParameter('mailer_from_address') => $this->container->getParameter('mailer_from_name'));

        $template = $this->twig->loadTemplate('BackendBundle:Mail:agentInvitation.html.twig');
        $subject  = $template->renderBlock('subject', $data);
        $textBody = $template->renderBlock('body_text', $data);
        $htmlBody = $template->renderBlock('body_html', $data);

        $transport = \Swift_SmtpTransport::newInstance();

        $message = \Swift_Message::newInstance($transport)
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($agent->getEmail());

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
