<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\Plan;
use BackendBundle\Repository\PlanRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use BackendBundle\Entity\Subscription;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SubscriptionType
 */
class SubscriptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plan', EntityType::class, [
                'class'         => Plan::class,
                'choice_label'  => 'name',
                'query_builder' => function (PlanRepository $er) {
                    return $er->getFindAllQueryBuilder()->orderBy('a.name', 'asc');
                },
                'expanded'      => false,
                'multiple'      => false,
                'required'      => true,
            ])
            ->add('dueDate', DateType::class, [
                'attr' => array('class' => 'datepicker'),
                'widget'   => 'single_text',
                'format' => 'MM/dd/y',
                'label' => 'labels.due_date',
            ])
            ->add('status', ChoiceType::class, array(
                'label' => 'labels.status',
                'choices' => Subscription::$statuses,
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Subscription::class,
            'empty_value' => new Subscription(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'subscription';
    }
}
