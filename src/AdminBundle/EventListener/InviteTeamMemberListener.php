<?php
namespace AdminBundle\EventListener;

use AdminBundle\Event\InviteTeamMemberEvent;
use BackendBundle\Entity\TeamMember;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class InviteTeamMemberListener
 */
class InviteTeamMemberListener
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
     * @param InviteTeamMemberEvent $event
     */
    public function onInviteTeamMember(InviteTeamMemberEvent $event)
    {
        $message = $event->getMessage();
        $teamMember = $event->getTeamMember();

        if ($message === null || !$teamMember instanceof TeamMember) {
            return; // do nothing
        }

        $data =  array(
            'firstName'  => $teamMember->getInvitationEmail(),
            'email'      => $teamMember->getInvitationEmail(),
            'url'        => $this->container->get('router')->generate('admin_agent_team_member_accept_invitation', [
                'team' => $teamMember->getTeam()->getId(),
                'token' => $teamMember->getToken(),
            ], UrlGenerator::ABSOLUTE_URL),
            'signature'  => $this->container->getParameter('signature'),
            'message'    => $message,
            'teamMember' => $teamMember,
        );

        $from = array($this->container->getParameter('mailer_from_address') => $this->container->getParameter('mailer_from_name'));

        $template = $this->twig->loadTemplate('BackendBundle:Mail:invitationTeamMember.html.twig');
        $subject  = $template->renderBlock('subject', $data);
        $textBody = $template->renderBlock('body_text', $data);
        $htmlBody = $template->renderBlock('body_html', $data);

        $transport = \Swift_SmtpTransport::newInstance();

        $message = \Swift_Message::newInstance($transport)
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($teamMember->getInvitationEmail());

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
