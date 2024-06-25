<?php
/**
 * EPI License.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 *  Service to save comments to database.
 */
class UserService
{
    /**
     * @var UserRepository user repository
     */
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository rep
     *                                       Constructor
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user user to save
     *
     * @return void
     *              Save comment to database
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user, true);
    }
}
