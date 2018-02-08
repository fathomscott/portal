<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\Admin;
use BackendBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StaffType
 * @package AdminBundle\Form\Type
 */
class StaffType extends AbstractType
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
            ->add('last_name', null, ['required' => true])
            ->add('status', ChoiceType::class, ['choices' => User::$statuses])
            ->add('plain_password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'Your passwords do not match.',
                'required'        => $options['require_password'],
                'first_options'   => ['label' => 'labels.password'],
                'second_options'  => ['label' => 'labels.password_confirm'],
            ])
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Admin::class,
            'empty_value' => new Admin(),
            'require_password' => false,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'staff';
    }
}
