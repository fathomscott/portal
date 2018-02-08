<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\State;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StateType
 * @package AdminBundle\Form\Type
 */
class StateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, ['required' => true])
            ->add('name', TextType::class, ['required' => true])
            ->add('terminalPin', TextType::class, ['required' => true])
            ->add('merchantId', TextType::class, ['required' => true])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => State::class,
            'empty_value' => new State(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'state';
    }
}
