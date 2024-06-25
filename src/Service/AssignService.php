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
     * @param User|null $user user to add
     * @param Bug       $bug  bug to add user to
     *
     * @return string|null error
     *
     * assign new user to a bug
     */
    public function add(?User $user, Bug $bug): ?string
    {
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
     * @param User $user user to remove
     * @param Bug  $bug  bug to remove user from
     *
     * @return void
     *              unassign a user from the bug
     */
    public function remove(User $user, Bug $bug): void
    {
        $bug->removeAssigned($user);
        $this->bugRep->save($bug, true);
    }
}
