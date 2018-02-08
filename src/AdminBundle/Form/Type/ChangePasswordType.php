<?php
namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use BackendBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ChangePasswordType
 * @package AdminBundle\Form
 */
class ChangePasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'Your Passwords do not match.',
                'first_options'   => ['label' => 'labels.new_password'],
                'second_options'  => ['label' => 'labels.new_password_confirm'],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'empty_value' => new User(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'change_password';
    }
}
