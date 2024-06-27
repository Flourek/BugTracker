<?php
/**
 * EPI License.
 */

namespace App\Controller;

use App\Entity\Bug;
use App\Form\BugType;
use App\Repository\BugRepository;
use App\Service\BugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Page where you (or admin) can edit the submitted bug.
 */
class EditController extends AbstractController
{
    /**
     * Index page with form to edit bug.
     *
     * @param Request                       $request    request
     * @param BugRepository                 $bugRep     bugRep
     * @param BugService                    $bugService bugService
     * @param AuthorizationCheckerInterface $auth       auth
     * @param int                           $bugID      bugID
     *
     * @return Response http
     */
    #[\Symfony\Component\Routing\Attribute\Route('/edit/{bugID}', name: 'edit_index', requirements: ['bugID' => '[1-9]\d*'])]
    #[IsGranted('EDIT', subject: 'bugID')]
    public function index(Request $request, BugRepository $bugRep, BugService $bugService, AuthorizationCheckerInterface $auth, int $bugID): Response
    {
        $bug = $bugService->getBugByID($bugID);

        $form = $this->createForm(BugType::class, $bug);

        return $this->render('edit.html.twig', [
            'form' => $form->createView(),
            'bug' => $bug,
        ]);
    }

    /**
     * Handling form request to edit a post.
     *
     * @param Request    $request    request
     * @param int        $bugID      bugID
     * @param BugService $bugService bugService
     *
     * @return Response http
     */
    #[\Symfony\Component\Routing\Attribute\Route('/edit_post/{bugID}', name: 'edit_edit', methods: ['POST'], requirements: ['bugID' => '[1-9]\d*'])]
    #[IsGranted('EDIT', subject: 'bugID')]
    public function edit(Request $request, int $bugID, BugService $bugService): Response
    {
        $bug = $bugService->getBugByID($bugID);

        $form = $this->createForm(BugType::class, $bug);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bugService->edit($bug);
        }

        return $this->redirectToRoute('main_index', ['bugID' => $bugID]);
    }

    /**
     * Deletes the bug from the database.
     *
     * @param int                 $bugID      bugID
     * @param BugService          $bugService bugService
     * @param TranslatorInterface $translator translator
     *
     * @return Response http
     */
    #[\Symfony\Component\Routing\Attribute\Route('/delete/{bugID}', name: 'edit_delete', methods: ['POST'], requirements: ['bugID' => '[1-9]\d*'])]
    #[IsGranted('DELETE', subject: 'bugID')]
    public function delete(int $bugID, BugService $bugService, TranslatorInterface $translator): Response
    {
        $bug = $bugService->getBugByID($bugID);
        $bugService->delete($bug);
        $this->addFlash('info', "Bug-{$bugID} ".$translator->trans('has been deleted'));

        return $this->redirectToRoute('main_index');
    }
}
