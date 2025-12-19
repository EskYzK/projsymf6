<?php

namespace App\Form\Product;

use App\Form\Data\ProductFlowDTO;
use App\Form\Product\Step\ProductDetailsStepType;
use App\Form\Product\Step\ProductTypeStepType;
use Symfony\Component\Form\Flow\AbstractFlowType; // Classe native Symfony 7.4 pour les flows
use Symfony\Component\Form\Flow\FormFlowBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFlowType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder
            ->addStep('type', ProductTypeStepType::class)
            ->addStep('details', ProductDetailsStepType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductFlowDTO::class,
        ]);
    }
}