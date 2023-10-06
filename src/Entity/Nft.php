<?php

namespace App\Entity;

use App\Repository\NftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NftRepository::class)]
class Nft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['allUsers','oneUser', 'allNfts', 'oneNft', 'allCategories', 'oneSubCategory'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['allUsers','oneUser', 'allNfts', 'oneNft', 'allCategories', 'oneSubCategory'])]
    private ?int $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['allUsers','oneUser', 'allNfts', 'oneNft', 'allCategories', 'oneSubCategory'])]
    private ?\DateTimeInterface $createdAt = null;
    #[ORM\ManyToOne(inversedBy: 'nfts', )]
    #[Groups(['allNfts', 'oneNft'])]
    private ?User $user = null;


    #[ORM\ManyToMany(targetEntity: SubCategory::class, inversedBy: 'nfts')]
    #[Groups(['allNfts', 'oneNft'])]
    private Collection $subCategories;

    #[ORM\Column(length: 255)]
    #[Groups(['allNfts', 'oneNft', 'allSubCategories', 'oneSubCategory'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['allNfts', 'oneNft', 'allCategories', 'oneSubCategory'])]
    private ?string $src = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['allNfts', 'oneNft', 'allCategories', 'oneSubCategory'])]
    private ?int $weight = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['allNfts', 'oneNft', 'allCategories', 'oneSubCategory'])]
    private ?string $format = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['allNfts', 'oneNft', 'allCategories', 'oneSubCategory'])]
    private ?string $description = null;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

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


    /**
     * @return Collection<int, SubCategory>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(SubCategory $subCategory): static
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories->add($subCategory);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): static
    {
        $this->subCategories->removeElement($subCategory);

        return $this;
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

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): static
    {
        $this->src = $src;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): static
    {
        $this->format = $format;

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

}
