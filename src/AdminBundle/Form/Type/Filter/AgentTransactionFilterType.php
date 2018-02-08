<?php
namespace AdminBundle\Form\Type\Filter;

use BackendBundle\Entity\User;
use BackendBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use BackendBundle\Entity\Transaction;

/**
 * Class AgentTransactionFilterType
 */
class AgentTransactionFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromCreated', TextType::class, [
                'attr' => array('class' => 'datepicker'),
                'label' => 'labels.date_added_from',
                'required' => false,
            ])
            ->add('toCreated', TextType::class, [
                'attr' => array('class' => 'datepicker'),
                'label' => 'labels.date_added_to',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => Transaction::$statuses,
                'label' => 'Status',
                'required' => false,
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'transaction_filter';
    }
}
