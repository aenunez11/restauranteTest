<?php

namespace Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Segment
{
    private $id;

    private $uidentifier;

    private $name;

    private $createdAt;

    private $deletedAt;

    private $averagePopularityRate;

    private $averageSatisfactionRate;

    private $averagePrice;

    private $totalReviews;

    private $restaurants;

    public function __construct()
    {
        $this->restaurants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUidentifier(): ?string
    {
        return $this->uidentifier;
    }

    public function setUidentifier(string $uidentifier): self
    {
        $this->uidentifier = $uidentifier;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getAveragePopularityRate(): ?string
    {
        return $this->averagePopularityRate;
    }

    public function setAveragePopularityRate(?string $averagePopularityRate): self
    {
        $this->averagePopularityRate = $averagePopularityRate;

        return $this;
    }

    public function getAverageSatisfactionRate(): ?string
    {
        return $this->averageSatisfactionRate;
    }

    public function setAverageSatisfactionRate(?string $averageSatisfactionRate): self
    {
        $this->averageSatisfactionRate = $averageSatisfactionRate;

        return $this;
    }

    public function getAveragePrice(): ?string
    {
        return $this->averagePrice;
    }

    public function setAveragePrice(?string $averagePrice): self
    {
        $this->averagePrice = $averagePrice;

        return $this;
    }

    public function getTotalReviews(): ?int
    {
        return $this->totalReviews;
    }

    public function setTotalReviews(?int $totalReviews): self
    {
        $this->totalReviews = $totalReviews;

        return $this;
    }

    /**
     * @return Collection|Restaurant[]
     */
    public function getRestaurants(): Collection
    {
        return $this->restaurants;
    }

    public function addRestaurant(Restaurant $restaurant): self
    {
        if (!$this->restaurants->contains($restaurant)) {
            $this->restaurants[] = $restaurant;
        }

        return $this;
    }

    public function removeRestaurant(Restaurant $restaurant): self
    {
        if ($this->restaurants->contains($restaurant)) {
            $this->restaurants->removeElement($restaurant);
        }

        return $this;
    }
}
