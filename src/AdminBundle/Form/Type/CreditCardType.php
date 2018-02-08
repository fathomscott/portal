<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\CreditCard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CreditCardType
 * @package AdminBundle\Form\Type
 */
class CreditCardType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['required' => true])
            ->add('lastName', TextType::class, ['required' => true])
            ->add('cardNumber', TextType::class, ['required' => true])
            ->add('cvv', TextType::class, ['required' => true, 'label' => 'labels.cvv'])
            ->add('expMonth', TextType::class, ['required' => true, 'label' => 'labels.expiration_month'])
            ->add('expYear', TextType::class, ['required' => true, 'label' => 'labels.expiration_year'])
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CreditCard::class,
            'empty_value' => new CreditCard(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'credit_card';
    }
}
