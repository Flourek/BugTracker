<?php
/**
 * EPI License.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * The 404 page.
 */
class PageNotFoundController extends AbstractController
{
    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/not_found', name: 'not_found_index')]
    public function index(): Response
    {
        return $this->render(
            'pageNotFound.html.twig'
        );
    }
}
