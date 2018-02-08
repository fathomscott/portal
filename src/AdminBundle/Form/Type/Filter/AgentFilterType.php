<?php
namespace AdminBundle\Form\Type\Filter;

use BackendBundle\Entity\Subscription;
use BackendBundle\Entity\District;
use BackendBundle\Repository\DistrictRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AgentFilterType
 */
class AgentFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
/*
            ->add('firstName', TextType::class, [
                'label' => 'labels.first_name',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'labels.last_name',
                'required' => false,
            ])
*/
	    ->add('name', TextType::class, [
		'label' => 'labels.name',
		'required' => false,
	    ])
            ->add('email', TextType::class, [
                'label' => 'labels.email',
                'required' => false,
            ])
	    ->add('district', EntityType::class, [
		'class' => District::class,
                'choice_label' => 'name',
		'label' => 'labels.district',
		'required' => false,
		'query_builder' => function (DistrictRepository $dr) {
			return $dr->createQueryBuilder('d')
				->orderBy('d.name', 'ASC');
		},
	    ])
            ->add('status', ChoiceType::class, [
                //'choices' => Subscription::$statuses,
                'choices' => array("Active" => 1, "Inactive" => 0),
		'data' => 1,
                'label' => 'labels.status',
                'required' => false,
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'agent_filter';
    }
}
