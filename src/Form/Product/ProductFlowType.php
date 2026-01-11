<?php

namespace App\Form\Product;

use App\Form\Data\ProductFlowDTO;
use App\Form\Product\Step\ProductDetailsStepType;
use App\Form\Product\Step\ProductLicenseStepType;
use App\Form\Product\Step\ProductLogisticsStepType;
use App\Form\Product\Step\ProductTypeStepType;
use Symfony\Component\Form\Flow\AbstractFlowType;
use Symfony\Component\Form\Flow\FormFlowBuilderInterface;
use Symfony\Component\Form\Flow\Type\NavigatorFlowType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFlowType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder
            // ÉTAPE 1 : On ajoute inherit_data => true
            ->addStep('category', ProductTypeStepType::class, [
                'inherit_data' => true,
            ])
            
            // ÉTAPE 2 : Pareil ici
            ->addStep('details', ProductDetailsStepType::class, [
                'inherit_data' => true,
            ])
            
            // ÉTAPE 3 (Conditionnelle) : Pareil ici
            ->addStep('logistics', ProductLogisticsStepType::class, [
                'enabled' => fn (ProductFlowDTO $dto) => $dto->type === 'physique',
                'inherit_data' => true,
            ])
            
            // ÉTAPE 4 (Conditionnelle) : Pareil ici
            ->addStep('license', ProductLicenseStepType::class, [
                'enabled' => fn (ProductFlowDTO $dto) => $dto->type === 'numerique',
                'inherit_data' => true,
            ]);

        $builder->add('navigator', NavigatorFlowType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductFlowDTO::class,
            'step_property_path' => 'currentStep',
        ]);
    }
}