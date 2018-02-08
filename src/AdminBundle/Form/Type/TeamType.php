<?php
namespace AdminBundle\Form\Type;

use BackendBundle\Entity\District;
use BackendBundle\Entity\Team;
use BackendBundle\Entity\User;
use BackendBundle\Repository\DistrictRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class TeamType
 * @package AdminBundle\Form\Type
 */
class TeamType extends AbstractType
{
    /**
     * @var User
     */
    private $user;

    /**
     * TeamType constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct($tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['required' => true])
            ->add('district', EntityType::class, [
                'class' => District::class,
                'choice_label' => 'name',
                'query_builder' => function (DistrictRepository $repository) {
                    return $repository->getByAgentQueryBuilder($this->user);
                },
                'required' => true,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Team::class,
            'empty_value' => new Team(),
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'team';
    }
}
