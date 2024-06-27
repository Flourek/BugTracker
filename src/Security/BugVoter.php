<?php
/**
 * EPI License.
 */

namespace App\Security;

use App\Entity\Bug;
use App\Entity\User;
use App\Service\BugService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Security voter class based on the bug entity.
 */
class BugVoter extends Voter
{
    // these strings are just invented: you can use anything
    public const CREATE = 'CREATE';
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    public const COMMENT = 'COMMENT';
    public const ASSIGN = 'ASSIGN';
    public const CHANGE_STATUS = 'CHANGE_STATUS';

    private Bug $bug;

    /**
     * @param Security   $security   auth
     * @param BugService $bugService auth
     *                               Constructor
     */
    public function __construct(private Security $security, private BugService $bugService)
    {
        $this->bug = new Bug();
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
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE, self::COMMENT, self::ASSIGN, self::CHANGE_STATUS])) {
            return false;
        }

        if (!$subject instanceof Bug) {
            $id = intval($subject);

            if (is_int($id)) {
                $this->bug = $this->bugService->getBugByID($id);

                return true;
            }

            return false;
        } else {
            $this->bug = $subject;
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

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::VIEW => $this->canView(),
            self::CREATE => $this->canCreate($user),
            self::EDIT => $this->canEdit($user),
            self::DELETE => $this->canDelete($user),
            self::COMMENT => $this->canComment($user),
            self::ASSIGN => $this->canAssign($user),
            self::CHANGE_STATUS => $this->canChangeStatus($user),
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
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canDelete(?User $user): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canCreate(?User $user): bool
    {
        return (bool) $user;
    }

    /**
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canComment(?User $user): bool
    {
        return $this->canCreate($user);
    }

    /**
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canAssign(?User $user): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canChangeStatus(?User $user): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * @param User|null $user user
     *
     * @return bool permission
     */
    private function canEdit(?User $user): bool
    {
        return $user === $this->bug->getAuthor();
    }
}
