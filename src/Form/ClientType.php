<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('firstname', null, ['label' => 'Prénom'])
        ->add('lastname', null, ['label' => 'Nom'])
        ->add('email', null, ['label' => 'Email'])
        ->add('phonenumber', null, ['label' => 'Téléphone'])
        ->add('address', null, ['label' => 'Adresse postale']);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }

}
