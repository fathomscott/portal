<?php
namespace BackendBundle\EventListener;

use BackendBundle\Entity\Agent;
use BackendBundle\Event\MLSDuesEmailAdminEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class MLSDuesEmailAdminListener
 */
class MLSDuesEmailAdminListener
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
     * @param MLSDuesEmailAdminEvent $event
     */
    public function onSendMLSDuesToAdmin(MLSDuesEmailAdminEvent $event)
    {
        $fixedMLSDuesTransactions = $event->getFixedMLSDuesTransactions();
        $variableMLSDuesTransactions = $event->getVariableMLSDuesTransactions();

        $onboardingEmail = $this->container->get('backend.system_default_manager')->getSystemDefault()->getOnboardingEmail();
        $data =  array(
            'signature' => $this->container->getParameter('signature'),
            'fixedMLSDuesTransactions' => $fixedMLSDuesTransactions,
            'variableMLSDuesTransactions' => $variableMLSDuesTransactions,
        );

        $from = array($this->container->getParameter('mailer_from_address') => $this->container->getParameter('mailer_from_name'));

        $template = $this->twig->loadTemplate('@Backend/Mail/MLSDuesAdminNotification.html.twig');
        $subject  = $template->renderBlock('subject', $data);
        $textBody = $template->renderBlock('body_text', $data);
        $htmlBody = $template->renderBlock('body_html', $data);

        $transport = \Swift_SmtpTransport::newInstance();

        $message = \Swift_Message::newInstance($transport)
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($onboardingEmail);

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
