<?php
namespace AdminBundle\Security;

use BackendBundle\Entity\AgentNote;
use BackendBundle\Entity\SuperAdmin;
use BackendBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AgentNoteVoter
 */
class AgentNoteVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW   = 'view';
    const DELETE = 'delete';

    /**
     * @param string $attribute
     * @param mixed  $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::VIEW, self::DELETE))) {
            return false;
        }

        // only vote on Agent objects inside this voter
        if (!$subject instanceof AgentNote) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var AgentNote $agentNote */
        $agentNote = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $user->getId() === $agentNote->getAuthor()->getId();
            case self::DELETE:
                return $user->getId() === $agentNote->getAuthor()->getId();
        }

        throw new \LogicException('This code should not be reached!');
    }
}
