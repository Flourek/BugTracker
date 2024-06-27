<?php
/**
 * EPI License.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\AssignType;
use App\Form\StatusType;
use App\Service\AssignService;
use App\Service\StatusService;
use App\Service\BugService;
use App\Entity\Bug;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Landing page of the app. Sidebar with bugs to choose from, displays the currently selected bug.
 */
class MainController extends AbstractController
{
    /**
     * @param AuthorizationCheckerInterface $authorizationChecker auth
     * @param AssignService                 $assignService        service
     * @param CommentService                $commentService       service
     * @param BugService                    $bugService           service
     * @param StatusService                 $statusService        service
     *                                                            constructor
     */
    public function __construct(private AuthorizationCheckerInterface $authorizationChecker, private AssignService $assignService, private CommentService $commentService, private BugService $bugService, private StatusService $statusService)
    {
    }

    /**
     * @param Request $request http
     * @param int     $bugID   id
     *
     * @return Response http response
     *
     * Index page that displays info about the selected bug
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{bugID}', name: 'main_index', requirements: ['bugID' => '[1-9]\d*'], defaults: ['bugID' => -1])]
    public function index(Request $request, int $bugID): Response
    {
        // Shows the first bug in the list if one wasn't selected
        $activeBug = -1 === $bugID ? $this->bugService->getDefaultBug() : $this->bugService->getBugByID($bugID);

        if (!isset($activeBug)) {
            return $this->redirectToRoute('not_found_index');
        }

        // Bugs list to display in the sidebar
        $page =  $request->query->getInt('page', 1);
        $pagination = $this->bugService->paginate($page);

        $attachments = $activeBug->getAttachments();
        $comments = $activeBug->getComments();

        $commentForm = $this->createForm(CommentType::class);
        $assignForm = $this->createForm(AssignType::class);
        $statusForm = $this->createForm(StatusType::class);

        return $this->render(
            'main.html.twig',
            [
                'pagination' => $pagination,
                'activeBug' => $activeBug,
                'commentForm' => $commentForm->createView(),
                'statusForm' => $statusForm->createView(),
                'assignForm' => $assignForm->createView(),
                'assignFormError' => null,
                'comments' => $comments,
                'attachments' => $attachments,
                'assignedUsers' => $activeBug->getAssigned(),
            ]
        );
    }

    /**
     * Handle assign form.
     *
     * @param Request $request request
     * @param int     $bugID   bugID
     *
     * @return Response http
     */
    #[\Symfony\Component\Routing\Attribute\Route('/assign/{bugID}', name: 'assign', methods: ['POST'], requirements: ['id' => '[1-9]\d*'], defaults: ['id' => -1])]
    #[IsGranted('ASSIGN', subject: 'bugID')]
    public function assign(Request $request, int $bugID): Response
    {
        $assignForm = $this->createForm(AssignType::class);
        $assignForm->handleRequest($request);

        if ($assignForm->isSubmitted() && $assignForm->isValid()) {
            $data = $assignForm->getData();
            $username = $data['username'];
            $toDelete = $data['toDelete'];

            if (isset($username)) {
                $this->assignService->add($username, $bugID);
            }

            if (isset($toDelete)) {
                $this->assignService->remove(intval($toDelete), $bugID);
            }
        }

        return $this->redirectToRoute('main_index', ['bugID' => $bugID]);
    }

    /**
     * Handle changing status form request.
     *
     * @param Request $request request
     * @param int     $bugID   bugID
     *
     * @return Response http
     */
    #[\Symfony\Component\Routing\Attribute\Route('/status/{bugID}', name: 'status', methods: ['POST'], requirements: ['id' => '[1-9]\d*'], defaults: ['id' => -1])]
    #[IsGranted('CHANGE_STATUS', subject: 'bugID')]
    public function status(Request $request, int $bugID): Response
    {
        $statusForm = $this->createForm(StatusType::class);
        $statusForm->handleRequest($request);
        $activeBug = $this->bugService->getBugByID($bugID);

        if ($statusForm->isSubmitted() && $statusForm->isValid()) {
            $data = $statusForm->getData();
            if (isset($data['value'])) {
                $this->statusService->set($activeBug, $data['value']);
            }
        }

        return $this->redirectToRoute('main_index', ['bugID' => $bugID]);
    }

    /**
     * Handle submitting new comments.
     *
     * @param Request $request request
     * @param int     $bugID   bugID
     *
     * @return Response http
     */
    #[\Symfony\Component\Routing\Attribute\Route('/comment/{bugID}', name: 'comment', methods: ['POST'], requirements: ['id' => '[1-9]\d*'], defaults: ['id' => -1])]
    #[IsGranted('COMMENT', subject: 'bugID')]
    public function comment(Request $request, int $bugID): Response
    {
        $activeBug = $this->bugService->getBugByID($bugID);
        $newComment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $newComment);
        $commentForm->handleRequest($request);

        // Handle request for creating a new comment
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->commentService->create($newComment, $activeBug);
        }

        return $this->redirectToRoute('main_index', ['bugID' => $bugID]);
    }
}
