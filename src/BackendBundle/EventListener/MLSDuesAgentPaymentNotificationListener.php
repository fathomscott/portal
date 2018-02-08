<?php
namespace BackendBundle\EventListener;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\District;
use BackendBundle\Entity\Transaction;
use BackendBundle\Event\MLSDuesAgentPaymentNotificationEvent;
use BackendBundle\Event\MLSDuesEmailAdminEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class MLSDuesAgentPaymentNotificationListener
 */
class MLSDuesAgentPaymentNotificationListener
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
     * @param MLSDuesAgentPaymentNotificationEvent $event
     */
    public function onSendNotification(MLSDuesAgentPaymentNotificationEvent $event)
    {
        $transaction = $event->getTransaction();
        $district = $transaction->getDistrict();
        $agent = $transaction->getUser();

        if (!$transaction instanceof Transaction || !$district instanceof District || !$agent instanceof Agent) {
            return; // do nothing
        }

        $data =  array(
            'firstName'   => $agent->getFirstName(),
            'email'       => $agent->getEmail(),
            'url'         => $this->container->get('router')->generate('admin_document_index', [], UrlGenerator::ABSOLUTE_URL),
            'signature'   => $this->container->getParameter('signature'),
            'district'    => $district,
            'transaction' => $transaction,
        );

        $from = array($this->container->getParameter('mailer_from_address') => $this->container->getParameter('mailer_from_name'));

        if ($transaction->getStatus() === Transaction::STATUS_APPROVED) {
            $template = $this->twig->loadTemplate('@Backend/Mail/MLSDuesAgentPaymentNotificationSuccess.html.twig');
        } else {
            $template = $this->twig->loadTemplate('@Backend/Mail/MLSDuesAgentPaymentNotificationFailure.html.twig');
        }
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
