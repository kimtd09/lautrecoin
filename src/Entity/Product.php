<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $publish_date = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'product', orphanRemoval: true, cascade:["persist"])]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $region = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'product')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'products', cascade:["persist"])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Department $department = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column]
    private ?int $zipcode = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $list_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_small_url = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $image_number = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favorites')]
    private Collection $favorite_of_users;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SourceUser $sourceUser = null;

     public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->favorite_of_users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPublishDate(): ?\DateTimeImmutable
    {
        return $this->publish_date;
    }

    public function setPublishDate(\DateTimeImmutable $publish_date): static
    {
        $this->publish_date = $publish_date;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(int $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getListId(): ?string
    {
        return $this->list_id;
    }

    public function setListId(string $list_id): static
    {
        $this->list_id = $list_id;

        return $this;
    }

    public function getImageSmallUrl(): ?string
    {
        return $this->image_small_url;
    }

    public function setImageSmallUrl(?string $image_small_url): static
    {
        $this->image_small_url = $image_small_url;

        return $this;
    }

    public function getImageNumber(): ?int
    {
        return $this->image_number;
    }

    public function setImageNumber(int $image_number): static
    {
        $this->image_number = $image_number;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavoriteOfUsers(): Collection
    {
        return $this->favorite_of_users;
    }

    public function addFavoriteOfUser(User $favoriteOfUser): static
    {
        if (!$this->favorite_of_users->contains($favoriteOfUser)) {
            $this->favorite_of_users->add($favoriteOfUser);
            $favoriteOfUser->addFavorite($this);
        }

        return $this;
    }

    public function removeFavoriteOfUser(User $favoriteOfUser): static
    {
        if ($this->favorite_of_users->removeElement($favoriteOfUser)) {
            $favoriteOfUser->removeFavorite($this);
        }

        return $this;
    }

    public function getSourceUser(): ?SourceUser
    {
        return $this->sourceUser;
    }

    public function setSourceUser(?SourceUser $sourceUser): static
    {
        $this->sourceUser = $sourceUser;

        return $this;
    }

}
