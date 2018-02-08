<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\TeamMember;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class TeamMemberType
 */
class TeamMemberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invitationEmail', EmailType::class, [
                'required' => true,
                'label' => 'labels.email',
                'constraints' => [new NotBlank(), new Email()],
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TeamMember::class,
            'empty_value' => new TeamMember(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'team_member';
    }
}
