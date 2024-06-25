<?php
/**
 * EPI License.
 */

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Standard user class.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'Username already taken')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    private ?string $plainPassword;

    #[ORM\Column(length: 64)]
    private ?string $password;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Bug::class, orphanRemoval: true)]
    private Collection $bugs;

    #[ORM\ManyToMany(targetEntity: Bug::class, mappedBy: 'assigned')]
    private Collection $assignedTo;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->bugs = new ArrayCollection();
        $this->assignedTo = new ArrayCollection();
    }

    /**
     * @return int|null id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string username
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @return string password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password password to set
     *
     * @return $this this user
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $username username to set
     *
     * @return $this this user
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string the username - an unique identifier
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @return array|string[] array of roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles roles to set
     *
     * @return $this this user
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see UserInterface
     *
     * @return string|null salt
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Comment> the comments of the user
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment comment to add
     *
     * @return $this this user
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment comment to remove
     *
     * @return $this this user
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bug> the user's reported bugs
     */
    public function getBugs(): Collection
    {
        return $this->bugs;
    }

    /**
     * @param Bug $bug the bug to add
     *
     * @return $this this user
     */
    public function addBug(Bug $bug): self
    {
        if (!$this->bugs->contains($bug)) {
            $this->bugs->add($bug);
            $bug->setAuthor($this);
        }

        return $this;
    }

    /**
     * @param Bug $bug the bug to remove
     *
     * @return $this this user
     */
    public function removeBug(Bug $bug): self
    {
        if ($this->bugs->removeElement($bug)) {
            // set the owning side to null (unless already changed)
            if ($bug->getAuthor() === $this) {
                $bug->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bug> which bugs the user is assigned to
     */
    public function getAssignedTo(): Collection
    {
        return $this->assignedTo;
    }

    /**
     * @param Bug $assignedTo bug to assign the user to
     *
     * @return $this this user
     */
    public function addAssignedTo(Bug $assignedTo): self
    {
        if (!$this->assignedTo->contains($assignedTo)) {
            $this->assignedTo->add($assignedTo);
            $assignedTo->addAssigned($this);
        }

        return $this;
    }

    /**
     * @param Bug $assignedTo bug to unassign the user from
     *
     * @return $this this user
     */
    public function removeAssignedTo(Bug $assignedTo): self
    {
        if ($this->assignedTo->removeElement($assignedTo)) {
            $assignedTo->removeAssigned($this);
        }

        return $this;
    }
}
