<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\Document;
use BackendBundle\Entity\DocumentOption;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DocumentType
 * @package AdminBundle\Form\TypeN
 */
class DocumentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('documentOption', EntityType::class, [
                'class' => DocumentOption::class,
                'choice_label' => 'name',
                'group_by' => function ($documentOption) {
                    return $documentOption->isRequired() ? 'Required' : 'Optional';
                },
                'choice_attr' => function ($documentOption) {
                    return ['expiration-date-required' => $documentOption->isExpirationDateRequired()];
                },
                'attr' => ['class' => 'document-option' ]
            ])
            ->add('description', TextType::class, ['required' => false])
            ->add('documentFile', FileType::class)
            ->add('expiration_date', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker document-expiration-date'],
                'format' => 'MM/dd/yyyy',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Document::class,
            'empty_value' => new Document(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'document';
    }
}
