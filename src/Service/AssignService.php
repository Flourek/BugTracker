<?php
/**
 * EPI License.
 */

namespace App\Service;

use App\Entity\Bug;
use App\Entity\User;
use App\Repository\BugRepository;
use App\Repository\UserRepository;

/**
 * Service for assigning users to resolve bugs.
 */
class AssignService
{
    private BugRepository $bugRep;

    private UserRepository $userRep;

    /**
     * @param UserRepository $userRep       rep
     * @param BugRepository  $bugRepository rep
     *                                      Constructor
     */
    public function __construct(UserRepository $userRep, BugRepository $bugRepository)
    {
        $this->bugRep = $bugRepository;
        $this->userRep = $userRep;
    }

    /**
     * checks if user is assigned to bug.
     *
     * @param string $username username
     * @param int    $bugID    bugID
     *
     * @return bool whether if assigned
     */
    public function isAssigned(string $username, int $bugID): bool
    {
        $user = $this->userRep->findOneBy(['username' => $username]);
        $bug = $this->bugRep->findOneBy(['id' => $bugID]);

        return $bug->isAssigned($user);
    }

    /**
     * assign new user to a bug.
     *
     * @param string $username username
     * @param int    $bugID    bug id
     *
     * @return string error
     */
    public function add(string $username, int $bugID): ?string
    {
        $user = $this->userRep->findOneBy(['username' => $username]);
        $bug = $this->bugRep->findOneBy(['id' => $bugID]);

        if (isset($user)) {
            if ($bug->isAssigned($user)) {
                return false;
            }

            $bug->addAssigned($user);

            $this->bugRep->save($bug, true);
        } else {
            return false;
        }

        return null;
    }

    /**
     * unassign a user from the bug.
     *
     * @param int $userID user id
     * @param int $bugID  bug id
     *
     * @return void e
     */
    public function remove(int $userID, int $bugID): void
    {
        $user = $this->userRep->findOneBy(['id' => $userID]);
        $bug = $this->bugRep->findOneBy(['id' => $bugID]);

        if (isset($user) && isset($bug)) {
            $bug->removeAssigned($user);
            $this->bugRep->save($bug, true);
        }
    }
}
