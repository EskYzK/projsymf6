<?php

namespace App\Form\Product;

use App\Form\Data\ProductFlowDTO;
use App\Form\Product\Step\ProductDetailsStepType;
use App\Form\Product\Step\ProductLicenseStepType;
use App\Form\Product\Step\ProductLogisticsStepType;
use App\Form\Product\Step\ProductTypeStepType;
use Symfony\Component\Form\Flow\DefaultFlowType;
use Symfony\Component\Form\Flow\FormFlowBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFlowType extends DefaultFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder
            ->addStep('type', ProductTypeStepType::class)
            ->addStep('details', ProductDetailsStepType::class)
            
            ->addStep('logistics', ProductLogisticsStepType::class, [
                'enabled' => fn (ProductFlowDTO $dto) => $dto->type === 'physique',
            ])
            
            ->addStep('license', ProductLicenseStepType::class, [
                'enabled' => fn (ProductFlowDTO $dto) => $dto->type === 'numerique',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductFlowDTO::class,
        ]);
    }
}