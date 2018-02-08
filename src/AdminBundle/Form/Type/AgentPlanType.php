<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\Plan;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AgentPlanType
 * @package AdminBundle\Form\Type
 */
class AgentPlanType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plan', EntityType::class, [
                'class' => Plan::class,
                'required' => true,
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'state';
    }
}
