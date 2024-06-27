<?php
/**
 * EPI License.
 */

namespace App\Controller;

use App\Entity\Bug;
use App\Form\BugType;
use App\Service\BugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Page where you fill out a report (bug), supports markdown, multiple files and after submitting it redirects you to the newly created post.
 */
class ReportController extends AbstractController
{
    /**
     * @param Request    $request    http
     * @param BugService $bugService bug service
     *
     * @return Response response
     *                  Index action
     */
    #[\Symfony\Component\Routing\Attribute\Route('/report', name: 'report_index')]
    public function index(Request $request, BugService $bugService): Response
    {
        $bug = new Bug();

        $this->denyAccessUnlessGranted('CREATE', $bug);

        $form = $this->createForm(BugType::class, $bug);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFiles = $request->files->get('bug')['imageFiles'];

            $bug = $bugService->create($this->getUser(), $bug, $uploadedFiles);

            // redirect to the newly created post
            return $this->redirectToRoute('main_index', ['bugID' => $bug->getId()]);
        }

        return $this->render('report.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
