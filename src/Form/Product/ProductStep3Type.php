<?php

namespace App\Form\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductStep3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['is_physical']) {
            $builder->add('stock', IntegerType::class, [
                'label' => 'Quantité en stock',
                'help' => 'Nombre d\'unités disponibles'
            ]);
        }
        else {
            $builder->add('licenseKey', TextType::class, [
                'label' => 'Clé de licence / Lien',
                'help' => 'Le lien de téléchargement ou la clé à envoyer'
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'is_physical' => true,
        ]);
    }
}