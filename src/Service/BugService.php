<?php
/**
 * EPI License.
 */

namespace App\Service;

use App\Entity\Bug;
use App\Entity\User;
use App\Repository\BugRepository;
use App\Type\StatusEnum;

/**
 * Service to get and save bugs from the database.
 */
class BugService
{
    /**
     * @var BugRepository rep
     */
    private BugRepository $bugRep;

    /**
     * @var uploadService service
     */
    private UploadService $uploadService;

    /**
     * @param BugRepository $bugRepository rep
     * @param UploadService $uploadService service
     *
     *                                     Constructor
     */
    public function __construct(BugRepository $bugRepository, UploadService $uploadService)
    {
        $this->bugRep = $bugRepository;
        $this->uploadService = $uploadService;
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
