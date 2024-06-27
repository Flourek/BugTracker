<?php
/**
 * EPI License.
 */

namespace App\Service;

use App\Entity\Bug;
use App\Entity\User;
use App\Repository\BugRepository;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Type\StatusEnum;

/**
 * Service to get and save bugs from the database.
 */
class BugService
{
    /**
     * @var BugRepository rep
     */

    /**
     * @var uploadService service
     */

    /**
     * @param BugRepository      $bugRep        bug repository
     * @param CommentRepository  $cmRep         comment repository
     * @param UploadService      $uploadService service
     * @param PaginatorInterface $paginator     paginator
     *                                          Constructor
     */
    public function __construct(private BugRepository $bugRep, private CommentRepository $cmRep, private UploadService $uploadService, private PaginatorInterface $paginator)
    {
    }

    /**
     * @param User       $author the author of the bug
     * @param Bug        $bug    the bug with body, title filled out
     * @param mixed|null $files  attachments
     *
     * @return Bug bug
     *
     * Create a new bug with default values
     */
    public function create(User $author, Bug $bug, mixed $files = null): Bug
    {
        $bug->setAuthor($author);
        $bug->setCreatedAt(new \DateTimeImmutable('now'));
        $bug->setStatus(StatusEnum::UNRESOLVED);

        $this->uploadService->saveFiles($files, $bug);

        $this->bugRep->save($bug, true);

        return $bug;
    }

    /**
     * @param Bug $bug the bug with body, title filled out
     *
     * @return Bug bug
     *
     * Edit a bug
     */
    public function edit(Bug $bug): Bug
    {
        $this->bugRep->save($bug, true);

        return $bug;
    }

    /**
     * @param Bug $bug the bug with body, title filled out
     *
     *                 Edit a bug
     */
    public function delete(Bug $bug): void
    {
        $comments = $bug->getComments();
        foreach ($comments as $cm) {
            $this->cmRep->remove($cm);
        }

        $this->bugRep->remove($bug, true);
    }

    /**
     * paginate.
     *
     * @param int $page the current page of the pagination
     *
     * @return mixed pagination result
     */
    public function paginate(int $page): mixed
    {
        $pagination = $this->paginator->paginate(
            $this->bugRep->queryAll(),
            $page,
            BugRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $pagination;
    }

    /**
     * @param int $id id
     *
     * @return Bug|null bug with that id
     *
     * retrieve bug from repository by its ID
     */
    public function getBugByID(int $id): ?Bug
    {
        return $this->bugRep->find($id);
    }

    /**
     * @return Bug|null sample bug
     *                  retrieve a sample bug
     */
    public function getDefaultBug(): ?Bug
    {
        return $this->bugRep->findOneBy([], ['id' => 'ASC']);
    }
}
