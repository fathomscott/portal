<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\BillingOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BillingOptionType
 * @package AdminBundle\Form\Type
 */
class BillingOptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => true])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => BillingOption::class,
            'empty_value' => new BillingOption(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'billing_option';
    }
}
