<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\DocumentOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DocumentOptionType
 * @package AdminBundle\Form\Type
 */
class DocumentOptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('required', CheckboxType::class, ['required' => false])
            ->add('expirationDateRequired', CheckboxType::class, ['required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DocumentOption::class,
            'empty_value' => new DocumentOption(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'document_option';
    }
}
