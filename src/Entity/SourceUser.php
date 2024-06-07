<?php

namespace App\Entity;

use App\Repository\SourceUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SourceUserRepository::class)]
class SourceUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'sourceUser')]
    private Collection $products;

    #[ORM\Column(length: 255)]
    private ?string $user_source_id = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setSourceUser($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSourceUser() === $this) {
                $product->setSourceUser(null);
            }
        }

        return $this;
    }

    public function getUserSourceId(): ?string
    {
        return $this->user_source_id;
    }

    public function setUserSourceId(string $user_source_id): static
    {
        $this->user_source_id = $user_source_id;

        return $this;
    }
}
