<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_name', fields: ['name'])]
#[UniqueEntity(fields: ['name'], message: "duplicated names aren't allowed")]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'region', orphanRemoval: true)]
    private Collection $products;

    /**
     * @var Collection<int, OldRegion>
     */
    #[ORM\OneToMany(targetEntity: OldRegion::class, mappedBy: 'new_name')]
    private Collection $oldRegions;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->oldRegions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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
            $product->setRegion($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getRegion() === $this) {
                $product->setRegion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OldRegion>
     */
    public function getOldRegions(): Collection
    {
        return $this->oldRegions;
    }

    public function addOldRegion(OldRegion $oldRegion): static
    {
        if (!$this->oldRegions->contains($oldRegion)) {
            $this->oldRegions->add($oldRegion);
            $oldRegion->setNewName($this);
        }

        return $this;
    }

    public function removeOldRegion(OldRegion $oldRegion): static
    {
        if ($this->oldRegions->removeElement($oldRegion)) {
            // set the owning side to null (unless already changed)
            if ($oldRegion->getNewName() === $this) {
                $oldRegion->setNewName(null);
            }
        }

        return $this;
    }
}
