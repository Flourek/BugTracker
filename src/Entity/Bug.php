<?php
/**
 * EPI License.
 */

namespace App\Entity;

use App\Repository\BugRepository;
use App\Type\StatusEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Issue, report, bug - displayed on the main page, supports markdown, has info to recreate the bug, has status (Resolved, Unresolved...).
 */
#[ORM\Entity(repositoryClass: BugRepository::class)]
class Bug
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 0, max: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 4096)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 0, max: 4096)]
    private ?string $body = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'bug', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'bugs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'assignedTo')]
    private Collection $assigned;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $enviroment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $version = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    private ?string $statusString = null;

    #[ORM\OneToMany(mappedBy: 'bug', targetEntity: Attachment::class, orphanRemoval: true)]
    private Collection $attachments;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->assigned = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    /**
     * @return int|null get id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null get title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title set title
     *
     * @return $this this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null get body
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string $body set body
     *
     * @return $this this
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null get the date it was created
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt set the date it was created
     *
     * @return $this this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Comment> get collection of all the comments
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment the comment to add
     *
     * @return $this this
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBug($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment the comment to remove
     *
     * @return $this this
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBug() === $this) {
                $comment->setBug(null);
            }
        }

        return $this;
    }

    /**
     * @return User|null the author of the post
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author set the author of the post
     *
     * @return $this this
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, User> get the users assigned to the bug
     */
    public function getAssigned(): Collection
    {
        return $this->assigned;
    }

    /**
     * @param User $assigned user to check
     *
     * @return bool whether if user is assigned
     */
    public function isAssigned(User $assigned): bool
    {
        return $this->assigned->contains($assigned);
    }

    /**
     * @param User $assigned user to assign
     *
     * @return $this THIS
     */
    public function addAssigned(User $assigned): self
    {
        if (!$this->isAssigned($assigned)) {
            $this->assigned->add($assigned);
        }

        return $this;
    }

    /**
     * @param User $assigned user to remove from assigned
     *
     * @return $this THIS
     */
    public function removeAssigned(User $assigned): self
    {
        $this->assigned->removeElement($assigned);

        return $this;
    }

    /**
     * @return string|null gets the environment
     */
    public function getEnviroment(): ?string
    {
        return $this->enviroment;
    }

    /**
     * @param string|null $enviroment sets the environment
     *
     * @return $this THIS
     */
    public function setEnviroment(?string $enviroment): self
    {
        $this->enviroment = $enviroment;

        return $this;
    }

    /**
     * @return string|null get the version
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string|null $version set the version
     *
     * @return $this this
     */
    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string get the status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param StatusEnum $status the status to set
     *
     * @return $this this
     */
    public function setStatus(StatusEnum $status): self
    {
        $this->status = StatusEnum::convertToInt($status);

        return $this;
    }

    /**
     * @param int $status the status to set
     *
     * @return $this
     */
    public function setStatusInt(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Attachment> get the attachments of the bug
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    /**
     * @param Attachment $attachment attachment to add
     *
     * @return $this this
     */
    public function addAttachment(Attachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setBug($this);
        }

        return $this;
    }

    /**
     * @param Attachment $attachment the attachment to remove
     *
     * @return $this this
     */
    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getBug() === $this) {
                $attachment->setBug(null);
            }
        }

        return $this;
    }
}
