<?php

namespace App\Entity;

use App\Repository\SubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SubCategoryRepository::class)]
class SubCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['allNfts, oneNft'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['allNfts', 'oneNft'])]
    private ?string $subCategoryName = null;

    #[ORM\ManyToMany(targetEntity: Nft::class, mappedBy: 'subCategories')]
    private Collection $nfts;

    #[ORM\ManyToOne(inversedBy: 'subCategories')]
    private ?Category $category = null;

    public function __construct()
    {
        $this->nfts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubCategoryName(): ?string
    {
        return $this->subCategoryName;
    }

    public function setSubCategoryName(string $subCategoryName): static
    {
        $this->subCategoryName = $subCategoryName;

        return $this;
    }

    /**
     * @return Collection<int, Nft>
     */
    public function getNfts(): Collection
    {
        return $this->nfts;
    }

    public function addNft(Nft $nft): static
    {
        if (!$this->nfts->contains($nft)) {
            $this->nfts->add($nft);
            $nft->addSubCategory($this);
        }

        return $this;
    }

    public function removeNft(Nft $nft): static
    {
        if ($this->nfts->removeElement($nft)) {
            $nft->removeSubCategory($this);
        }

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
}
