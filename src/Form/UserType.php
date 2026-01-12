<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $options['data'] ?? null;
        $isEdit = $user && $user->getId();

        $builder
            ->add('email', EmailType::class)
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Manager' => 'ROLE_MANAGER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ]);

        if (!$isEdit) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length(['min' => 6, 'minMessage' => 'Au moins {{ limit }} caractères']),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}