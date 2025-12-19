<?php

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class ProductFlowDTO
{
    #[Assert\NotBlank(message: "Veuillez choisir un type de produit.")]
    public ?string $type = null;

    #[Assert\NotBlank]
    public ?string $name = null;

    public ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $price = null;

    public ?int $stock = null;
    public ?string $license = null;
}