<?php
namespace BackendBundle\EventListener;

use BackendBundle\Entity\Email;
use BackendBundle\Manager\EmailManager;

/**
 * Class EmailSendPerformedListener
 */
class EmailSendPerformedListener implements \Swift_Events_SendListener
{
    /**
     * @var EmailManager
     */
    private $emailManager;

    /**
     * @param \Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(\Swift_Events_SendEvent $evt)
    {
    }

    /**
     * @param \Swift_Events_SendEvent $event
     */
    public function sendPerformed(\Swift_Events_SendEvent $event)
    {
        $message = $event->getMessage();
        if ($event->getResult() !== \Swift_Events_SendEvent::RESULT_SPOOLED) {
            return;
        }

        $email = new Email();
        $email->setFrom(implode(', ', array_keys($message->getFrom())));
        $email->setTo(implode(', ', array_keys($message->getTo())));
        $email->setSubject($message->getSubject());
        $email->setBody($message->getBody());
        $email->setCreated(new \DateTime('now'));
        $this->emailManager->save($email);
    }

    /**
     * @param EmailManager $emailManager
     */
    public function setEmailManager(EmailManager $emailManager)
    {
        $this->emailManager = $emailManager;
    }
}
