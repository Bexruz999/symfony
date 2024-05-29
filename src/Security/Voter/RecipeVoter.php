<?php

namespace App\Security\Voter;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RecipeVoter extends Voter
{
    public const EDIT = 'RECIPE_EDIT';
    public const VIEW = 'RECIPE_VIEW';

    public const CREATE = 'RECIPE_CREATE';
    public const LIST = 'RECIPE_LIST';
    public const LIST_ALL = 'RECIPE_LIST_ALL';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return
            (in_array($attribute, [self::EDIT, self::VIEW]) && $subject instanceof \App\Entity\Recipe) ||
            in_array($attribute, [self::CREATE, self::LIST, self::LIST_ALL]);
    }

    /**
     * @param string $attribute
     * @Param Recipe|null $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /*if (!$subject instanceof Recipe) {
            return false;
        }*/

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
