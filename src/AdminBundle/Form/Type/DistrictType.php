<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\AgentDistrict;
use BackendBundle\Entity\District;
use BackendBundle\Entity\State;
use BackendBundle\Repository\AgentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DistrictType
 * @package AdminBundle\Form\Type
 */
class DistrictType extends AbstractType
{
    /**
     * @var AgentDistrict[]|PersistentCollection|null
     */
    private $agentDistricts;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => true])
            ->add('state', EntityType::class, [
                'class' => State::class,
                'required' => true,
            ])
            ->add('MLSDuesRequired', CheckboxType::class, [
                'required' => false,
                'label' => 'labels.mls_dues_required',
                'attr' => ['class' => 'dues-required'],
            ])
            ->add('MLSDuesType', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'label' => 'labels.mls_dues_type',
                'choices' => District::$duesTypes,
                'attr' => ['class' => 'dues-type'],
            ])
            ->add('MLSFee', TextType::class, [
                'required' => false,
                'label' => 'labels.mls_fee',
                'attr' => ['class' => 'mls-fee'],
            ])
        ;

        $builder->add(
            $builder->create('agentDistricts', EntityType::class, [
                'required' => false,
                'mapped' => true,
                'class' => Agent::class,
                'label' => 'labels.district_director',
                'query_builder' => function (AgentRepository $agentRepository) {
                    return $agentRepository->getFindAllQueryBuilder()->orderBy('a.firstName', 'ASC');
                },
                'attr' => ['class' => 'district-director'],
            ])
            ->addModelTransformer(new CallbackTransformer(
                function ($data) {
                    $this->agentDistricts = $data;
                    foreach ($data as $agentDistrict) { /** @var AgentDistrict $agentDistrict */
                        if ($agentDistrict->isDistrictDirector()) {
                            return $agentDistrict->getAgent();
                        }
                    }
                },
                function (Agent $newDD = null) {
                    /* DD - District director */
                    $oldDD = null;
                    foreach ($this->agentDistricts as $agentDistrict) {
                        /* remove agent from district if he selected as DD */
                        if (null !== $newDD && !$agentDistrict->isDistrictDirector() && $newDD->getId() === $agentDistrict->getAgent()->getId()) {
                            $this->agentDistricts->removeElement($agentDistrict);
                        }

                        if ($agentDistrict->isDistrictDirector()) {
                            $oldDD = $agentDistrict;
                        }
                    }

                    if ((null === $newDD && null !== $oldDD) || /* just remove */
                        (null !== $newDD && null !== $oldDD && $newDD->getId() !== $oldDD->getAgent()->getId()) /* change district director */
                    ) {
                        $this->agentDistricts->removeElement($oldDD);
                    }

                    if ((null !== $newDD && null === $oldDD) || /* add DD*/
                        (null !== $newDD && null !== $oldDD && $newDD->getId() !== $oldDD->getAgent()->getId()) /* change DD */
                    ) {
                        $agentDistrict = new AgentDistrict();
                        $agentDistrict->setDistrictDirector(true);
                        $agentDistrict->setAgent($newDD);

                        $this->agentDistricts->add($agentDistrict);
                    }

                    return $this->agentDistricts;
                }
            ))
        );

        /* add to created AgentDistrict association current District */
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $district = $event->getData(); /** @var $district District */

            foreach ($district->getAgentDistricts() as $agentDistrict) {
                if ($agentDistrict->getId() === null) {
                    $agentDistrict->setDistrict($district);
                }
            }
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => District::class,
            'empty_value' => new District(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'district';
    }
}
