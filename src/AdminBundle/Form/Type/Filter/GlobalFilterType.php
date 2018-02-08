<?php
namespace AdminBundle\Form\Type\Filter;

use AdminBundle\Utils\GlobalFilter;
use BackendBundle\Entity\District;
use BackendBundle\Entity\State;
use BackendBundle\Manager\DistrictManager;
use BackendBundle\Repository\DistrictRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class GlobalFilterType
 */
class GlobalFilterType extends AbstractType
{
    /**
     * @var array
     */
    private $stateChoices = [];

    /**
     * @var array
     */
    private $districtChoices = [];

    /**
     * @var GlobalFilter
     */
    private $globalFilter;

    /**
     * GlobalFilterType constructor.
     * @param GlobalFilter       $globalFilter
     * @param DistrictRepository $districtRepository
     */
    public function __construct(GlobalFilter $globalFilter, DistrictRepository $districtRepository)
    {
        $this->globalFilter = $globalFilter;

        /** @var District[] $districts */
        $districts = $districtRepository->getFindAllQueryBuilder()->getQuery()->getResult();
        foreach ($districts as $district) {
            $stateName = $district->getState()->getName();
            $stateId = $district->getState()->getId();

            $this->stateChoices[$stateName] = $stateId;

            if (!array_key_exists($stateName, $this->districtChoices)) {
                $this->districtChoices[$stateName] = [];
            }

            $this->districtChoices[$stateName][$stateName.' - '.$district->getName()] = $district->getId();
        }
	asort($this->stateChoices);
	ksort($this->districtChoices);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(GlobalFilter::STATE, ChoiceType::class, [
                'choices' => $this->stateChoices,
                'data' => $this->globalFilter->getStateValue(),
                'required' => false,
                'placeholder' => 'Global State Filter...',
            ])
            ->add(GlobalFilter::DISTRICT, ChoiceType::class, [
                'choices' => $this->districtChoices,
                'data' => $this->globalFilter->getDistrictValue(),
                'placeholder' => "Global Market Filter...",
                'required' => false,
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
