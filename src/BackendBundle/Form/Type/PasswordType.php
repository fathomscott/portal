<?php
namespace BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as BasePasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use BackendBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PasswordType
 */
class PasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type'            => BasePasswordType::class,
                'invalid_message' => 'Passwords did not match.',
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
        return 'password';
    }
}
