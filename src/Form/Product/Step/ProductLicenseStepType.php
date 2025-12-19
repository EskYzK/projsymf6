<?php

namespace App\Form\Product\Step;

use App\Form\Data\ProductFlowDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductLicenseStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('license', TextType::class, [
            'label' => 'Clé de licence / Lien d\'accès',
            'help' => 'L\'utilisateur recevra cette information après achat.'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ProductFlowDTO::class]);
    }
}