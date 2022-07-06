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

    #[ORM\ManyToOne(targetEntity: Car::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $car_id;

    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $station_id;

    #[ORM\Column(type: 'datetime', length: 255)]
    private $charge_start;

    #[ORM\Column(type: 'datetime', length: 255)]
    private $charge_end;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCarId(): ?Car
    {
        return $this->car_id;
    }

    public function setCarId(?Car $car_id): self
    {
        $this->car_id = $car_id;

        return $this;
    }

    public function getStationId(): ?Station
    {
        return $this->station_id;
    }

    public function setStationId(?Station $station_id): self
    {
        $this->station_id = $station_id;

        return $this;
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
}
