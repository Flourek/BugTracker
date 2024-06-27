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
                return 'This user is already assigned!';
            }

            $bug->addAssigned($user);

            $this->bugRep->save($bug, true);
        } else {
            return "This user doesn't exist!";
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
