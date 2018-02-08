<?php
namespace AdminBundle\Security;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\SuperAdmin;
use BackendBundle\Entity\Team;
use BackendBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class TeamVoter
 */
class TeamVoter extends Voter
{
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
        if (!$subject instanceof Team) {
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
     * @param Team $team
     * @param User $user
     * @return bool
     */
    private function canView(Team $team, User $user)
    {
        if ($user instanceof SuperAdmin) {
            return true;
        }

        if ($user instanceof Agent and $user->isDistrictDirector()) {
            $districtDirectorIds = [];

            foreach ($user->getAgentDistricts() as $agentDistrict) {
                if ($agentDistrict->isDistrictDirector()) {
                    $districtDirectorIds[] = $agentDistrict->getDistrict()->getId();
                }
            }

            if (in_array($team->getDistrict()->getId(), $districtDirectorIds, true)) {
                return true;
            }
        }

        return false;
    }
}
