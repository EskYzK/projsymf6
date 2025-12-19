<?php

namespace App\Form\Product\Step;

use App\Form\Data\ProductFlowDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductTypeStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('type', ChoiceType::class, [
            'choices' => [
                'Produit Physique' => 'physique',
                'Produit Numérique' => 'numerique',
            ],
            'expanded' => true,
            'label' => 'Quel type de produit souhaitez-vous ajouter ?'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ProductFlowDTO::class]);
    }
}