<?php
namespace AdminBundle\Form\Type\Filter;

use BackendBundle\Entity\DocumentOption;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DocumentFilterType
 */
class DocumentFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'labels.first_name',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'labels.last_name',
                'required' => false,
            ])
            ->add('documentOption', EntityType::class, [
                'class' => DocumentOption::class,
                'choice_label' => 'name',
                'required' => false,
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'document_filter';
    }
}
