<?php
/**
 * EPI License.
 */

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Overview of any user, includes all the user's activity: bugs, comments, bugs they're assigned to.
 */
class UserController extends AbstractController
{
    /**
     * @param UserRepository $rep      rep
     * @param string         $username the username of the user in question
     *
     * @return Response http
     *
     * Index action
     */
    #[\Symfony\Component\Routing\Attribute\Route('/user/{username}', name: 'user_index', defaults: ['username' => ''])]
    public function index(UserRepository $rep, string $username): Response
    {
        $user = '' === $username || '0' === $username ? $this->getUser() : $rep->findOneByUsername($username);

        if (isset($user)) {
            $comments = $user->getComments();
            $bugs = $user->getBugs();
            $assigned = $user->getAssignedTo();
        } else {
            return $this->redirectToRoute('not_found_index');
        }

        return $this->render(
            'user.html.twig',
            [
                'user' => $user,
                'comments' => $comments,
                'bugs' => $bugs,
                'assigned' => $assigned,
            ]
        );
    }
}
