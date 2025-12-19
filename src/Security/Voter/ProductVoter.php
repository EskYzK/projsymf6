<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter
{
    public const EDIT = 'PRODUCT_EDIT';
    public const DELETE = 'PRODUCT_DELETE';
    public const VIEW = 'PRODUCT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
            && $subject instanceof \App\Entity\Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($attribute === self::VIEW) {
            return true;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}