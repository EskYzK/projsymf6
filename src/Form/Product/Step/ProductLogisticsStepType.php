<?php

namespace App\Form\Product\Step;

use App\Form\Data\ProductFlowDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductLogisticsStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('stock', IntegerType::class, [
            'label' => 'Quantité en stock',
            'help' => 'Nombre d\'unités disponibles.'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ProductFlowDTO::class]);
    }
}