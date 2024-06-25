<?php
/**
 * EPI License.
 */

namespace App\Service;

use App\Entity\Bug;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;

/**
 *  Service to save comments to database.
 */
class CommentService
{
    private CommentRepository $commentRep;

    /**
     * @param CommentRepository $commentRep rep
     *                                      Constructor
     */
    public function __construct(CommentRepository $commentRep)
    {
        $this->commentRep = $commentRep;
    }

    /**
     * @param Comment $comment comment with body filled out
     * @param User    $author  the author of the comment
     * @param Bug     $bug     the bug that was commented
     *
     * @return Comment the resulting comment
     *
     * Save comment to database
     */
    public function create(Comment $comment, User $author, Bug $bug): Comment
    {
        $comment->setAuthor($author);
        $comment->setCreatedAt(new \DateTimeImmutable('now'));
        $comment->setBug($bug);

        $this->commentRep->save($comment, true);

        return $comment;
    }
}
