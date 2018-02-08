<?php
namespace BackendBundle\Validator\Constraints;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\AgentDistrict;
use BackendBundle\Repository\AgentDistrictRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ConstraintAgentDistrictValidator
 */
class ConstraintAgentDistrictValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var AgentDistrictRepository
     */
    private $agentDistrictRepository;

    /**
     * ConstraintAgentDistrictValidator constructor.
     * @param TranslatorInterface     $translator
     * @param AgentDistrictRepository $agentDistrictRepository
     */
    public function __construct(TranslatorInterface $translator, AgentDistrictRepository $agentDistrictRepository)
    {
        $this->translator = $translator;
        $this->agentDistrictRepository = $agentDistrictRepository;
    }

    /**
     * @param Agent      $agent
     * @param Constraint $constraint
     */
    public function validate($agent, Constraint $constraint)
    {
        $agentDistricts = $agent->getAgentDistricts();
        if ($agentDistricts->count() === 0) {
            return;
        }

        $districtIds = [];
        $districtsWithDirector = [];
        foreach ($agentDistricts as $agentDistrict) { /** @var AgentDistrict $agentDistrict */
            $districtId = $agentDistrict->getDistrict()->getId();
            if (in_array($districtId, $districtIds, true)) {
                $this->context->buildViolation($this->translator->trans('error.agent.agentDistricts.more_than_one', ['%district%' => $agentDistrict->getDistrict()->getName()]))
                    ->atPath('agentDistricts')
                    ->addViolation();

                return;
            }

            if ($agentDistrict->isDistrictDirector()) {
                $districtsWithDirector[] = $districtId;
            }

            $districtIds[] = $districtId;
        }

        $agentDistrictDirector = $this->agentDistrictRepository->checkIfDistrictsHaveDirector($districtsWithDirector, $agent);
        if (null !== $agentDistrictDirector) {
            $this->context->buildViolation($this->translator->trans('error.agent.agentDistricts.has_district_director', ['%district%' => $agentDistrictDirector->getDistrict()->getName()]))
                ->atPath('agentDistricts')
                ->addViolation();
        }
    }
}
