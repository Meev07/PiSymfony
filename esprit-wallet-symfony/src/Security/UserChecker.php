<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getIsActive()) {
            throw new CustomUserMessageAccountStatusException('Your account is blocked. Please contact the administrator.');
        }

        if (!$user->getIsVerified()) {
            throw new CustomUserMessageAccountStatusException('Your account is not verified. Please check your email for the verification code.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // We can re-check here if status changes during session, but pre-auth is usually enough
        if (!$user->getIsActive()) {
            throw new CustomUserMessageAccountStatusException('Your account is blocked. Please contact the administrator.');
        }
    }
}
