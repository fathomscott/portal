<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\Plan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StateType
 * @package AdminBundle\Form\Type
 */
class PlanType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => true])
            ->add('public', CheckboxType::class, ['required' => false])
            ->add('status', ChoiceType::class, ['required' => true, 'choices' => Plan::$statuses])
            ->add('monthlyPrice', NumberType::class, ['required' => true])
            ->add('annualPrice', NumberType::class, ['required' => true])
            ->add('referralDiscount', NumberType::class, ['required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Plan::class,
            'empty_value' => new Plan(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'plan';
    }
}
