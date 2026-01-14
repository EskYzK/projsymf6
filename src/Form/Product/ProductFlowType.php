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
            ->addStep('category', ProductTypeStepType::class, [
                'inherit_data' => true,
            ])
            
            ->addStep('details', ProductDetailsStepType::class, [
                'inherit_data' => true,
            ])
            
            ->addStep('logistics', ProductLogisticsStepType::class, [
                'enabled' => function (ProductFlowDTO $dto) {
                    return $dto->type === 'physique';
                },
                'inherit_data' => true,
            ])
            
            ->addStep('license', ProductLicenseStepType::class, [
                'enabled' => function (ProductFlowDTO $dto) {
                    return $dto->type === 'numerique';
                },
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