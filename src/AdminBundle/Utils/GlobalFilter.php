<?php
namespace AdminBundle\Utils;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\SuperAdmin;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GlobalFilter
 */
class GlobalFilter
{
    const STATE = 'global_filter_state';
    const DISTRICT = 'global_filter_district';

    /**
     * @var TokenStorageInterface
     */
    private $tokeStorage;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * GlobalFilter constructor.
     * @param TokenStorageInterface $tokeStorage
     * @param SessionInterface      $session
     */
    public function __construct(TokenStorageInterface $tokeStorage, SessionInterface $session)
    {
        $this->tokeStorage = $tokeStorage;
        $this->session = $session;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        $user = $this->tokeStorage->getToken()->getUser();

        if ($user instanceof SuperAdmin) {
            $stateId = $this->session->get(self::STATE);
            if (null !== $stateId) {
                return ['state' => $stateId];
            }

            $districtId = $this->session->get(self::DISTRICT);
            if (null !== $districtId) {
                return ['district' => [$districtId]];
            }
        }

        if ($user instanceof Agent and $user->isDistrictDirector()) {
            $districtIds = [];
            foreach ($user->getAgentDistricts() as $agentDistrict) {
                if ($agentDistrict->isDistrictDirector()) {
                    $districtIds[] = $agentDistrict->getDistrict()->getId();
                }
            }

            return ['district' => $districtIds];
        }

        return [];
    }

    /**
     * @return string
     */
    public function getStateValue()
    {
        return $this->session->get(self::STATE);
    }

    /**
     * @return string
     */
    public function getDistrictValue()
    {
        return $this->session->get(self::DISTRICT);
    }
}
