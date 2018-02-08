<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\AgentDistrict;
use BackendBundle\Entity\District;
use BackendBundle\Manager\AgentDistrictManager;
use BackendBundle\Manager\DistrictManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AgentDistrictType
 * @package AdminBundle\Form\Type
 */
class AgentDistrictType extends AbstractType
{
    /**
     * @var DistrictManager
     */
    private $districtManager;

    /**
     * @var AgentDistrictManager
     */
    private $agentDistrictManager;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('districtDirector', CheckboxType::class, ['required' => false]);
        $builder->add('primaryMarket', CheckboxType::class, ['required' => false]);

        $builder->add(
            $builder
                ->create('district', HiddenType::class)
                ->addModelTransformer(new CallbackTransformer(
                    function ($district) {
                        /** @var District $district */
                        if (null !== $district) {
                            return $district->getId();
                        }
                    },
                    function ($data) {
                        return $this->districtManager->find($data);
                    },
                    function ($data) {
                        return $this->primaryMarket->find($data);
                    }
                ))
                ->addViewTransformer((new CallbackTransformer(
                    function ($data) {
                        if (is_object($data)) {
                            return $data->getId();
                        }

                        return $data;
                    },
                    function ($data) {
                        return $this->districtManager->find($data);
                    },
                    function ($data) {
                        return $this->primaryMarket->find($data);
                    }
                )))
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AgentDistrict::class,
            'empty_value' => new AgentDistrict(),
        ));
    }

    /**
     * @param DistrictManager $districtManager
     */
    public function setDistrictManager($districtManager)
    {
        $this->districtManager = $districtManager;
    }

    /**
     * @param primaryMarket $primaryMarket
     */
    public function setPrimaryMarket($primaryMarket)
    {
        $this->primaryMarket = $primaryMarket;
    }

    /**
     * @param AgentDistrictManager $agentDistrictManager
     */
    public function setAgentDistrictManager($agentDistrictManager)
    {
        $this->agentDistrictManager = $agentDistrictManager;
    }

    /**
     * @param AgentPrimaryMarket $agentPrimaryMarket
     */
    public function setAgentPrimaryMarket($agentPrimaryMarket)
    {
        $this->agentPrimaryMarket = $agentPrimaryMarket;
    }
}
