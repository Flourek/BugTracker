<?php
/**
 * EPI License.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 *  Service to save comments to database.
 */
class UserService
{
    /**
     * @var UserRepository user repository
     */
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * @param UserRepository              $userRepository     rep
     * @param UserPasswordHasherInterface $userPasswordHasher hasher
     *
     *                                                        Constructor
     */
    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @param User $user user to save
     *
     * @return void nothing
     *              Save comment to database
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user, true);
    }

    /**
     * setNewPassword.
     *
     * @param User   $user          user
     * @param string $plainPassword the password in plain text
     *
     * @return void nothing
     */
    public function setNewPassword(User $user, string $plainPassword): void
    {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $plainPassword)
        );
    }

    /**
     * findByUsername.
     *
     * @param string $username username
     *
     * @return User user
     */
    public function findByUsername(string $username): User
    {
        return $this->userRepository->findOneByUsername($username);
    }

    /**
     * returns an array of the user's activity.
     *
     * @param User $user user
     *
     * @return mixed (comments, bugs, bugs they're assigned to)
     */
    public function getActivity(User $user): mixed
    {
        return [
            'comments'  => $user->getComments(),
            'bugs'      => $user->getBugs(),
            'assigned'  => $user->getAssignedTo(),
        ];
    }
}
