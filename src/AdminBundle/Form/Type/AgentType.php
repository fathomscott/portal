<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\District;
use BackendBundle\Entity\User;
use BackendBundle\Entity\State;
use BackendBundle\Repository\AgentRepository;
use BackendBundle\Repository\DistrictRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AgentType
 * @package AdminBundle\Form\Type
 */
class AgentType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['required' => true])
            ->add('first_name', null, ['required' => true])
            ->add('nick_name', null, ['required' => false, 'empty_data' => ''])
            ->add('middle_name', null, ['required' => false, 'empty_data' => ''])
            ->add('middle_initial', null, ['required' => false, 'empty_data' => ''])
            ->add('last_name', null, ['required' => true])
            ->add('suffix', null, ['required' => false, 'empty_data' => ''])
	    ->add('legal_name', null, ['required' => true])
            ->add('status', ChoiceType::class, ['choices' => User::$statuses])
            ->add('address')
	    ->add('city', null, ['required' => true])
            ->add('state', EntityType::class, [
                'class' => State::class,
                'required' => true,
            ])
	    ->add('zip', null, ['required' => true])
            ->add('phoneNumber')
            ->add('personalEmail')
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'format' => 'MM/dd/yyyy',
            ])
            ->add('joinedDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'format' => 'MM/dd/yyyy',
            ])
            ->add('socialSecurityNumber')
            ->add('ein', TextType::class, [
                'label' => 'labels.employer_id_number',
                'required' => false,
            ])
            ->add('pastFirm')
            ->add('RELExpirationDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'format' => 'MM/dd/yyyy',
                'label' => 'labels.real_estate_license_expiration_date',
                'required' => false,
            ])
            ->add('insuranceExpirationDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'format' => 'MM/dd/yyyy',
                'required' => false,
            ])
            ->add('districts')
            ->add('agentDistricts', CollectionType::class, [
                'required' => false,
                'entry_type' => AgentDistrictType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'error_bubbling' => false,
            ])
            ->add('referring_user', EntityType::class, [
                'class' => Agent::class,
                'query_builder' => function (AgentRepository $repository) {
                    return $repository->getFindAllQueryBuilder();
                },
		'choice_label' => function (User $entity = null) {
		    return $entity ? $entity->getLastName() . ', ' . $entity->getFirstName()  : '';
		},
                'required' => false,
            ])
            ->add('isMentor')
            ->add('isMentee')
        ;

        /* fetch districts based on current agent */
        $addDistrictSelect = function (FormInterface $form, Agent $agent, $mapped = true) {
            $form->add('districts', EntityType::class, [
                'class' => District::class,
                'query_builder' => function (DistrictRepository $repository) use ($agent) {
                    return $repository->getAllForAgentFormQueryBuilder($agent);
                },
                'choice_label' => function (District $district) {
                    return $district->getState()->getCode().' - '.$district->getName();
                },
                'group_by' => function (District $district) {
                    return  $district->getState()->getCode().' - '.$district->getState()->getName();
                },
                'choice_attr' => function (District $district) {
                    if (0 !== $district->getAgentDistricts()->count()) {
                        return ['disable-district-director' => true];
                    }

                    return [];
                },
                'required' => false,
                'multiple' => true,
                'mapped' => $mapped,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($addDistrictSelect) {
            $form = $event->getForm();
            $agent = $event->getData();

            $addDistrictSelect($form, $agent);
        });

        /* disable saving  */
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($addDistrictSelect) {
            $form = $event->getForm();
            $agent = $form->getNormData();

            $addDistrictSelect($form, $agent, false);
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Agent::class,
            'empty_value' => new Agent(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'agent';
    }
}
