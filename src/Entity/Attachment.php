<?php
/**
 * EPI License.
 */

namespace App\Entity;

use App\Repository\AttachmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Holds data for user uploaded files.
 */
#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
class Attachment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $originalName = null;

    #[ORM\ManyToOne(inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bug $bug = null;

    /**
     * @return int|null returns id of attachment
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null gets the path of attachment
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path sets the path of attachment
     *
     * @return $this this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string|null gets the filename
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    /**
     * @param string $originalName sets the filename
     *
     * @return $this this
     */
    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * @return Bug|null get the bug it's attached to
     */
    public function getBug(): ?Bug
    {
        return $this->bug;
    }

    /**
     * @param Bug|null $bug set the bug it's attached to
     *
     * @return $this this
     */
    public function setBug(?Bug $bug): self
    {
        $this->bug = $bug;

        return $this;
    }
}
