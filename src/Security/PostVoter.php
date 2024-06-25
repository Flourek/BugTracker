<?php
/**
 * EPI License.
 */

namespace App\Security;

use App\Entity\Bug;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Security voter class based on the bug entity.
 */
class PostVoter extends Voter
{
    // these strings are just invented: you can use anything
    public const CREATE = 'create';
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const COMMENT = 'comment';
    public const ASSIGN = 'assign';

    /**
     * @param Security $security auth
     *                           Constructor
     */
    public function __construct(private Security $security)
    {
    }

    /**
     * @param string $attribute current attribute
     * @param mixed  $subject   the entity (bug)
     *
     * @return bool whether if permission is granted
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE, self::COMMENT, self::ASSIGN])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Bug) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute current attribute
     * @param mixed          $subject   the entity (bug)
     * @param TokenInterface $token     auth
     *
     * @return bool permission
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Bug $bug */
        $bug = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView(),
            self::CREATE => $this->canCreate($bug, $user),
            self::EDIT => $this->canEdit($bug, $user),
            self::DELETE => $this->canDelete($bug, $user),
            self::COMMENT => $this->canComment($bug, $user),
            self::ASSIGN => $this->canAssign($bug, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    /**
     * @return bool permisison
     */
    private function canView(): bool
    {
        return true;
    }

    /**
     * @param Bug       $bug  bug
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canDelete(Bug $bug, ?User $user): bool
    {
        return $this->canEdit($bug, $user);
    }

    /**
     * @param Bug       $bug  bug
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canCreate(Bug $bug, ?User $user): bool
    {
        return (bool) $user;
    }

    /**
     * @param Bug       $bug  bug
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canComment(Bug $bug, ?User $user): bool
    {
        return $this->canCreate($bug, $user);
    }

    /**
     * @param Bug       $bug  bug
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canAssign(Bug $bug, ?User $user): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * @param Bug       $bug  bug
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canEdit(Bug $bug, ?User $user): bool
    {
        // this assumes that the Post object has a `getOwner()` method
        return $user === $bug->getAuthor();
    }
}
