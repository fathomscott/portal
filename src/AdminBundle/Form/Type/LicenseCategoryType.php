<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\LicenseCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LicenseCategoryType
 */
class LicenseCategoryType extends AbstractType
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
            'data_class' => LicenseCategory::class,
            'empty_value' => new LicenseCategory(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'license_category';
    }
}
