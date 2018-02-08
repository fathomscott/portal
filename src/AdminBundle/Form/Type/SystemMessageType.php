<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\SystemMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SystemMessageType
 * @package AdminBundle\Form\Type
 */
class SystemMessageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextareaType::class, [
                'required' => true,
                'attr' => ['rows' => 8],
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'format' => 'MM/dd/yyyy',
                'required' => false,
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'format' => 'MM/dd/yyyy',
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
            'data_class' => SystemMessage::class,
            'empty_value' => new SystemMessage(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'system_message';
    }
}
