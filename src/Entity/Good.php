<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GoodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(itemOperations={
 *          "get",
 *          "put"={"access_control"="is_granted('ROLE_API')"},
 *          "delete"={"access_control"="is_granted('ROLE_API')"}
 *      },
 *     collectionOperations={
 *          "post"={"access_control"="is_granted('ROLE_API')"}
 *      },
 *     normalizationContext={"groups"={"good"}},
 *     denormalizationContext={"groups"={"new_good"}}
 *     )
 * @ORM\Entity(repositoryClass=GoodRepository::class)
 */
class Good
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"good", "new_good"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"good", "new_good"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="goods", cascade={"persist"})
     * @Groups({"category","new_good"})
     */
    private $categories;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank
     * @Assert\GreaterThan(0)
     * @Groups({"good", "new_good"})
     */
    private $price;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
