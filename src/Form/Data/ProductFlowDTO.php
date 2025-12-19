<?php

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class ProductFlowDTO
{
    #[Assert\NotBlank]
    public ?string $type = null; // 'physique' ou 'numerique'

    #[Assert\NotBlank]
    public ?string $name = null;

    public ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public ?float $price = null;

    public ?int $stock = null;
    public ?string $licenseKey = null;
    
    public bool $confirmed = false;
}