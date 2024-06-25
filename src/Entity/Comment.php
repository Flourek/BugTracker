<?php
/**
 * EPI License.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comments posted by users in response to bugs.
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1024)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 0, max: 1024)]
    private ?string $body = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bug $bug = null;

    /**
     * @return int|null id of the comment
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null body of the comment
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string $body body of the comment
     *
     * @return $this this
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null the date of creation
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt date of creation to set
     *
     * @return $this this
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return User|null the author
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author author to set
     *
     * @return $this this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Bug|null the bug
     */
    public function getBug(): ?Bug
    {
        return $this->bug;
    }

    /**
     * @param Bug|null $bug to bug to set
     *
     * @return $this this
     */
    public function setBug(?Bug $bug): self
    {
        $this->bug = $bug;

        return $this;
    }
}
