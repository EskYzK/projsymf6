<?php

namespace App\Security\Voter;

use App\Entity\Client;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientVoter extends Voter
{
    public const EDIT = 'CLIENT_EDIT';
    public const DELETE = 'CLIENT_DELETE';
    public const ADD = 'CLIENT_ADD';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::ADD) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Client;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($this->security->isGranted('ROLE_MANAGER')) {
            if (in_array($attribute, [self::ADD, self::EDIT])) {
                return true;
            }
        }

        return false;
    }
}