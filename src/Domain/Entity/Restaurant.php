<?php

namespace Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Restaurant
{
    private $id;

    private $uidentifier;

    private $name;

    private $cityName;

    private $address;

    private $latitude;

    private $longitude;

    private $popularityRate;

    private $satisfactionRate;

    private $averagePrice;

    private $totalReviews;

    private $segments;

    public function __construct()
    {
        $this->segments = new ArrayCollection();
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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getPopularityRate(): ?string
    {
        return $this->popularityRate;
    }

    public function setPopularityRate(?string $popularityRate): self
    {
        $this->popularityRate = $popularityRate;

        return $this;
    }

    public function getSatisfactionRate(): ?string
    {
        return $this->satisfactionRate;
    }

    public function setSatisfactionRate(?string $satisfactionRate): self
    {
        $this->satisfactionRate = $satisfactionRate;

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
     * @return Collection|Segment[]
     */
    public function getSegments(): Collection
    {
        return $this->segments;
    }

    public function addSegment(Segment $segment): self
    {
        if (!$this->segments->contains($segment)) {
            $this->segments[] = $segment;
            $segment->addRestaurant($this);
        }

        return $this;
    }

    public function removeSegment(Segment $segment): self
    {
        if ($this->segments->contains($segment)) {
            $this->segments->removeElement($segment);
            $segment->removeRestaurant($this);
        }

        return $this;
    }

    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    public function setCityName($cityName): self
    {
        $this->cityName = $cityName;
    }


}
