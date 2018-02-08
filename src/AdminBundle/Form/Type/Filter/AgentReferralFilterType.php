<?php
namespace AdminBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AgentReferralFilterType
 */
class AgentReferralFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromDateAdded', TextType::class, [
                'attr' => array('class' => 'datepicker'),
                'label' => 'labels.date_added_from',
                'required' => false,
            ])
            ->add('toDateAdded', TextType::class, [
                'attr' => array('class' => 'datepicker'),
                'label' => 'labels.date_added_to',
                'required' => false,
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'referral_filter';
    }
}
