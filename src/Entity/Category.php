<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\CategoryRepository;
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
 *          "get",
 *          "post"={"access_control"="is_granted('ROLE_API')"}
 *      },
 *     normalizationContext={"groups"={"category"}},
 *     denormalizationContext={"groups"={"new_category"}},
 *     subresourceOperations={
 *          "api_categories_get_subresource"={
 *              "method"="GET",
 *              "path"="/api/categories/{id}/goods"
 *          }
 *      }
 *     )
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"good", "new_good", "category"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({
     *      "category",
     *      "good",
     *      "new_good",
     *      "new_category"
     * })
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Good::class, mappedBy="categories", cascade={"persist"})
     * @ApiSubresource(maxDepth=1)
     */
    private $goods;

    public function __construct()
    {
        $this->goods = new ArrayCollection();
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
     * @return Collection|Good[]
     */
    public function getGoods(): Collection
    {
        return $this->goods;
    }

    public function addGood(Good $good): self
    {
        if (!$this->goods->contains($good)) {
            $this->goods[] = $good;
            $good->addCategory($this);
        }

        return $this;
    }

    public function removeGood(Good $good): self
    {
        if ($this->goods->removeElement($good)) {
            $good->removeCategory($this);
        }

        return $this;
    }
}
