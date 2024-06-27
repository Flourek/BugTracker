<?php
/**
 * EPI License.
 */

namespace App\Service;

use App\Entity\Bug;
use App\Entity\User;
use App\Repository\BugRepository;

/**
 * Service for assigning users to resolve bugs.
 */
class StatusService
{
    private BugRepository $bugRep;

    /**
     * @param BugRepository $bugRepository rep
     *                                     Constructor
     */
    public function __construct(BugRepository $bugRepository)
    {
        $this->bugRep = $bugRepository;
    }

    /**
     * @param Bug $bug   bug to add user to
     * @param int $value the status
     *
     * @return string|null error
     *
     * set bug's status and flush it
     */
    public function set(Bug $bug, int $value): void
    {
        $bug->setStatusInt($value);

        $this->bugRep->save($bug, true);
    }
}
