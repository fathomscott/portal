<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\Agent;
use BackendBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AgentProfileType
 * @package AdminBundle\Form\Type
 */
class AgentProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['disabled' => true])
            ->add('first_name', null, ['required' => true])
            ->add('last_name', null, ['required' => true])
            ->add('address')
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
            ->add('referring_user', TextType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('isMentor')
            ->add('isMentee')
            ->add('headshotFile', FileType::class, [
                'label' => 'labels.headshot',
                'required' => false,
                'attr' => ['id' => 'imageFileInput'],
            ])
            ->add('cropImageData', HiddenType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => ['id' => 'cropImageData'],
            ])
        ;
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
