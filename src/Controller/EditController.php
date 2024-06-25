<?php
/**
 * EPI License.
 */

namespace App\Controller;

use App\Entity\Bug;
use App\Form\BugType;
use App\Repository\BugRepository;
use App\Repository\CommentRepository;
use App\Service\BugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Page where you (or admin) can edit the submitted bug.
 */
class EditController extends AbstractController
{
    /**
     * @param Request                       $request    request
     * @param BugRepository                 $bugRep     rep
     * @param BugService                    $bugService service
     * @param AuthorizationCheckerInterface $auth       auth
     * @param int                           $id         id
     *
     * @return Response http
     *                  Index page with form to edit bug
     */
    #[\Symfony\Component\Routing\Attribute\Route('/edit/{id}', name: 'edit_index', requirements: ['id' => '[1-9]\d*'])]
    public function index(Request $request, BugRepository $bugRep, BugService $bugService, AuthorizationCheckerInterface $auth, int $id): Response
    {
        $bug = $bugService->getBugByID($id);

        $this->denyAccessUnlessGranted('edit', $bug);

        $form = $this->createForm(BugType::class, $bug);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bugRep->save($bug, true);

            return $this->redirectToRoute('main_index', ['id' => $bug->getId()]);
        }

        return $this->render('edit.html.twig', [
            'form' => $form->createView(),
            'bug' => $bug,
        ]);
    }

    /**
     * @param int               $id         id
     * @param BugRepository     $bugRep     bugRep
     * @param CommentRepository $cmRep      cmRep
     * @param BugService        $bugService bugService
     *
     * @return Response http
     *                  Deletes the bug from the database
     */
    #[\Symfony\Component\Routing\Attribute\Route('/delete/{id}', name: 'edit_delete', requirements: ['id' => '[1-9]\d*'])]
    public function delete(int $id, BugRepository $bugRep, CommentRepository $cmRep, BugService $bugService): Response
    {
        $bug = $bugService->getBugByID($id);

        $this->denyAccessUnlessGranted('delete', $bug);

        $comments = $bug->getComments();
        foreach ($comments as $cm) {
            $cmRep->remove($cm);
        }

        $bugRep->remove($bug, true);

        return $this->redirectToRoute('main_index');
    }
}
