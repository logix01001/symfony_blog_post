<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var Post[]|Collection
     *
     * @ORM\ManyToMany(targetEntity=Post::class)
     * @ORM\JoinTable(name="post_tag")
     * @ORM\OrderBy({"name": "ASC"})
     *
     */
    private $posts;

    

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function addPost(Post ...$posts): void
    {
        foreach ($posts as $post) {
            if (!$this->posts->contains($post)) {
                $this->posts->add($post);
            }
        }
    }

    public function removeTag(Post $post): void
    {
        $this->posts->removeElement($post);
    }

    public function getTags(): Collection
    {
        return $this->posts;
    }

}
