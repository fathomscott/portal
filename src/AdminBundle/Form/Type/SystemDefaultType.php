<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\SystemDefault;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SystemDefaultType
 * @package AdminBundle\Form\Type
 */
class SystemDefaultType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dunningDays', TextType::class, [
                'required' => false,
            ])
            ->add('onboardingEmail', TextType::class, [
                'required' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SystemDefault::class,
            'empty_value' => new SystemDefault(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'system_default';
    }
}
