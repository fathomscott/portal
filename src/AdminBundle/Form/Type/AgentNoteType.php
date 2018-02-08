<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\AgentNote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AgentNoteType
 * @package AdminBundle\Form\Type
 */
class AgentNoteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', TextType::class, ['required' => true])
            ->add('public', CheckboxType::class, ['required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AgentNote::class,
            'empty_value' => new AgentNote(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'agent_note';
    }
}
