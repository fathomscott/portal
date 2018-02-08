<?php
namespace AdminBundle\Security;

use BackendBundle\Entity\Admin;
use BackendBundle\Entity\Agent;
use BackendBundle\Entity\AgentDistrict;
use BackendBundle\Entity\SuperAdmin;
use BackendBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AgentVoter
 */
class AgentVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';

    /**
     * @param string $attribute
     * @param mixed  $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::VIEW))) {
            return false;
        }

        // only vote on Agent objects inside this voter
        if (!$subject instanceof Agent) {
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

        /** @var Agent $agent */
        $agent = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($agent, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Agent $agent
     * @param User  $user
     * @return bool
     */
    private function canView(Agent $agent, User $user)
    {
        if ($user instanceof SuperAdmin || $user instanceof Admin) {
            return true;
        }

        if ($user instanceof Agent and $user->isDistrictDirector()) {
            $districtDirectorIds = [];

            foreach ($user->getAgentDistricts() as $agentDistrict) {
                if ($agentDistrict->isDistrictDirector()) {
                    $districtDirectorIds[] = $agentDistrict->getDistrict()->getId();
                }
            }

            foreach ($agent->getAgentDistricts() as $agentDistrict) {
                if (in_array($agentDistrict->getDistrict()->getId(), $districtDirectorIds, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
