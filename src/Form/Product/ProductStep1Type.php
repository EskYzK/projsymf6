<?php

namespace App\Form\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductStep1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('type', ChoiceType::class, [
            'label' => 'Type de produit',
            'choices' => [
                'Produit Physique (Stock)' => 'physique',
                'Produit Numérique (Licence)' => 'numerique',
            ],
            'expanded' => true,
            'label_attr' => ['class' => 'text-lg font-bold mb-4'],
        ]);
    }
}