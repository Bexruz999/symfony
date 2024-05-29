<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCollectionVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'COLLECTION_VIEW';
    public const CREATE = 'COLLECTION_CREATE';
    public const LIST = 'COLLECTION_LIST';
    public const LIST_ALL = 'COLLECTION_LIST_ALL';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW]) && $subject instanceof \App\Entity\UserCollection ||
            in_array($attribute, [self::CREATE, self::LIST, self::LIST_ALL]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $subject->getUser()->getId() === $user->getId();
                break;

            case self::LIST_ALL:
                return $this->security->isGranted('ROLE_ADMIN');

            case self::CREATE:
            case self::LIST:
            case self::VIEW:
                return true;
        }

        return false;
    }
}
