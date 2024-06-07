<?php

namespace App\Entity;

use App\Repository\OldRegionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OldRegionRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_old_name', fields: ['old_name'])]
class OldRegion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $old_name = null;

    #[ORM\ManyToOne(inversedBy: 'oldRegions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $new_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOldName(): ?string
    {
        return $this->old_name;
    }

    public function setOldName(string $old_name): static
    {
        $this->old_name = $old_name;

        return $this;
    }

    public function getNewName(): ?Region
    {
        return $this->new_name;
    }

    public function setNewName(?Region $new_name): static
    {
        $this->new_name = $new_name;

        return $this;
    }
}
