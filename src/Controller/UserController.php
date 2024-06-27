<?php
/**
 * EPI License.
 */

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Overview of any user, includes all the user's activity: bugs, comments, bugs they're assigned to.
 */
class UserController extends AbstractController
{
    /**
     * @param string      $username    the username of the user in question
     * @param UserService $userService user service
     *
     * @return Response http
     *
     * Index action
     */
    #[\Symfony\Component\Routing\Attribute\Route('/user/{username}', name: 'user_index', defaults: ['username' => ''])]
    public function index(string $username, UserService $userService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = '' === $username || '0' === $username ? $this->getUser() : $userService->findByUsername($username);
        $acivity = [];

        if (isset($user)) {
            $acivity = $userService->getActivity($user);
        } else {
            return $this->redirectToRoute('not_found_index');
        }

        return $this->render(
            'user.html.twig',
            [
                'user' => $user,
                'comments' => $acivity['comments'],
                'bugs'     => $acivity['bugs'],
                'assigned' => $acivity['assigned'],
            ]
        );
    }
}
