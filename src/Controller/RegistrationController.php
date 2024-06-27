<?php
/**
 * EPI License.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

/**
 * Page with registration form.
 */
class RegistrationController extends AbstractController
{
    /**
     * Function to register a new user.
     *
     * @param Request                     $request            request
     * @param UserPasswordHasherInterface $userPasswordHasher userPasswordHasher
     * @param UserService                 $userService        userService
     * @param UserAuthenticatorInterface  $userAuthenticator  userAuthenticator
     * @param AppAuthenticator            $authenticator      authenticator
     *
     * @return Response http
     */
    #[\Symfony\Component\Routing\Attribute\Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserService $userService, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $plainPassword = $form->get('plainPassword')->getData();
            $userService->setNewPassword($user, $plainPassword);

            $userService->save($user);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'registrationForm' => $form->createView(),
            ]
        );
    }
}
