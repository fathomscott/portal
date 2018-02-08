<?php
namespace BackendBundle\EventListener;

use BackendBundle\Event\BillSubscriptionEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class BillSubscriptionListener
 */
class BillSubscriptionListener
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
     * @param BillSubscriptionEvent $event
     */
    public function onSendNotification(BillSubscriptionEvent $event)
    {
        $subscription = $event->getSubscription();
        $agent = $subscription->getUser();
        $amount = $event->getAmount();
        $referralDiscount = $event->getReferralDiscount();

        $data =  array(
            'firstName'        => $agent->getFirstName(),
            'email'            => $agent->getEmail(),
            'url'              => $this->container->get('router')->generate('admin_document_index', [], UrlGenerator::ABSOLUTE_URL),
            'signature'        => $this->container->getParameter('signature'),
            'amount'           => $amount,
            'referralDiscount' => $referralDiscount,
        );

        $from = array($this->container->getParameter('mailer_from_address') => $this->container->getParameter('mailer_from_name'));
        $template = $this->twig->loadTemplate('@Backend/Mail/BillSubscriptionAgentNotification.html.twig');
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
