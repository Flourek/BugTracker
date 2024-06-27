<?php
/**
 * EPI License.
 */

namespace App\Service;

use App\Entity\Bug;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\Security\Core\Security;

/**
 *  Service to save comments to database.
 */
class CommentService
{
    /**
     * Constructor.
     *
     * @param CommentRepository $cmRep    rep
     * @param Security          $security security
     *
     * @return void
     */
    public function __construct(private CommentRepository $cmRep, private Security $security)
    {
    }

    /**
     * @param Comment $comment comment with body filled out
     * @param Bug     $bug     the bug that was commented
     *
     * @return Comment the resulting comment
     *
     * Save comment to database
     */
    public function create(Comment $comment, Bug $bug): Comment
    {
        $comment->setAuthor($this->security->getUser());
        $comment->setCreatedAt(new \DateTimeImmutable('now'));
        $comment->setBug($bug);

        $this->cmRep->save($comment, true);

        return $comment;
    }
}
