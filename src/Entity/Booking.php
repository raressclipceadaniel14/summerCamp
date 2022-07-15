<?php

namespace App\Entity;

use App\Repository\BookingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingsRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime', length: 255)]
    private $charge_start;

    #[ORM\Column(type: 'datetime', length: 255)]
    private $charge_end;

    #[ORM\ManyToOne(targetEntity: Car::class, inversedBy: 'bookings')]
    private $car;

    #[ORM\ManyToOne(targetEntity: Station::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private $station;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChargeStart(): ?\DateTime
    {
        return $this->charge_start;
    }

    public function setChargeStart(\DateTime $charge_start): self
    {
        $this->charge_start = $charge_start;

        return $this;
    }

    public function getChargeEnd(): ?\DateTime
    {
        return $this->charge_end;
    }

    public function setChargeEnd(\DateTime $charge_end): self
    {
        $this->charge_end = $charge_end;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): self
    {
        $this->car = $car;

        return $this;
    }

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): self
    {
        $this->station = $station;

        return $this;
    }
}
