<?php
/**
 * EPI License.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\BugRepository;
use App\Repository\UserRepository;
use App\Service\AssignService;
use App\Service\BugService;
use App\Service\CommentService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Landing page of the app. Sidebar with bugs to choose from, displays the currently selected bug.
 */
class MainController extends AbstractController
{
    private AuthorizationCheckerInterface $authorizationChecker;
    private UserRepository $userRep;

    private BugService $bugService;
    private CommentService $commentService;
    private AssignService $assignService;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker auth
     * @param AssignService                 $assignService        service
     * @param CommentService                $commentService       service
     * @param BugService                    $bugService           service
     * @param UserRepository                $userRep              rep
     *                                                            constructor
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, AssignService $assignService, CommentService $commentService, BugService $bugService, UserRepository $userRep)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->userRep = $userRep;
        $this->bugService = $bugService;
        $this->commentService = $commentService;
        $this->assignService = $assignService;
    }

    /**
     * @param         $activeBug  the current bug
     * @param         $assignForm the assign form
     * @param Request $request    request
     *
     * @return string|null possible error
     *
     * function to handle the request of the form for assigning users to a bug
     */
    public function handleAssignForm($activeBug, $assignForm, Request $request): ?string
    {
        $assignForm->handleRequest($request);

        $error = null;

        if ($assignForm->isSubmitted() && $assignForm->isValid()) {
            $data = $assignForm->getData();

            if (isset($data['username'])) {
                $user = $this->userRep->findOneBy(['username' => $data['username']]);

                $error = $this->assignService->add($user, $activeBug);
            }

            if (isset($data['toDelete'])) {
                $user = $this->userRep->find($data['toDelete']);
                $this->assignService->remove($user, $activeBug);
            }
        }

        return $error;
    }

    /**
     * @param Request            $request   http
     * @param BugRepository      $bugRep    bugrep
     * @param PaginatorInterface $paginator paginator
     * @param int                $id        id
     *
     * @return Response http response
     *
     * Index page that displays info about the selected bug
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}', name: 'main_index', requirements: ['id' => '[1-9]\d*'], defaults: ['id' => -1])]
    public function index(Request $request, BugRepository $bugRep, PaginatorInterface $paginator, int $id): Response
    {
        // Shows the first bug in the list if one wasn't selected
        $activeBug = -1 === $id ? $this->bugService->getDefaultBug() : $this->bugService->getBugByID($id);

        if (!isset($activeBug)) {
            return $this->redirectToRoute('not_found_index');
        }
        $this->denyAccessUnlessGranted('view', $activeBug);

        // Bugs list to display in the sidebar
        $pagination = $paginator->paginate(
            $bugRep->queryAll(),
            $request->query->getInt('page', 1),
            BugRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        $attachments = $activeBug->getAttachments();

        // Display comments and new comment form
        $comments = $activeBug->getComments();
        $newComment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $newComment);
        $commentForm->handleRequest($request);

        // Handle request for creating a new comment
        if ($this->isGranted('comment', $activeBug) && ($commentForm->isSubmitted() && $commentForm->isValid())) {
            $this->commentService->create($commentForm->getData(), $this->getUser(), $activeBug);

            return $this->redirect($request->getUri());
        }

        // Form to assign users to the bug as admin
        $assignFormError = null;

        $assignForm = $this->createFormBuilder()
            ->add('username', TextType::class, ['allow_extra_fields' => true])
            ->add('toDelete')
            ->add('save', SubmitType::class)
            ->getForm();

        $statusForm = $this->createFormBuilder(null, ['allow_extra_fields' => true])
            ->add('value', null, ['attr' => ['hidden' => 'true']])
            ->add('saveStatus', SubmitType::class)
            ->getForm();

        if ($this->isGranted('assign', $activeBug)) {
            $assignFormError = $this->handleAssignForm($activeBug, $assignForm, $request);
        }

        if ($this->isGranted('edit', $activeBug)) {
            $statusForm->handleRequest($request);

            if ($statusForm->isSubmitted() && $statusForm->isValid()) {
                $data = $statusForm->getData();
                if (isset($data['value'])) {
                    $activeBug->setStatusInt($data['value']);
                }
            }
        }

        return $this->render(
            'main.html.twig',
            [
                'pagination' => $pagination,
                'activeBug' => $activeBug,
                'commentForm' => $commentForm->createView(),
                'statusForm' => $statusForm->createView(),
                'assignForm' => $assignForm->createView(),
                'assignFormError' => $assignFormError,
                'comments' => $comments,
                'attachments' => $attachments,
                'assignedUsers' => $activeBug->getAssigned(),
            ]
        );
    }
}
