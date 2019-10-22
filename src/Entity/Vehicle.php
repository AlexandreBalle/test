<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VehicleRepository")
 */
class Vehicle
{
    const VOITURE = "voiture";
    const SCOOTER = "scooter";

    public static $types = [
        self::VOITURE,
        self::SCOOTER
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $model;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $km;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      min = 7,
     *      max = 8,
     *      minMessage = "Ton numero de matricule doit comporter au minimum {{ limit }} chiffres",
     *      maxMessage = "Ton numero de matricule doit comporter au maximum {{ limit }} chiffres"
     * )
     */
    private $matriculation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $photo = "image-not-found.png";

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $autonomy;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Choice({3, 5 , 7}, message="Ce nombre de porte est impossible")
     */
    private $door;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="vehicles")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Announce", cascade={"persist", "remove"})
     */
    private $announce;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getKm(): ?int
    {
        return $this->km;
    }

    public function setKm(?int $km): self
    {
        $this->km = $km;

        return $this;
    }

    public function getMatriculation(): ?string
    {
        return $this->matriculation;
    }

    public function setMatriculation(?string $matriculation): self
    {
        $this->matriculation = $matriculation;

        return $this;
    }

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(?\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getAutonomy(): ?int
    {
        return $this->autonomy;
    }

    public function setAutonomy(?int $autonomy): self
    {
        $this->autonomy = $autonomy;

        return $this;
    }

    public function getDoor(): ?int
    {
        return $this->door;
    }

    public function setDoor(?int $door): self
    {
        $this->door = $door;

        return $this;
    }

    public function getPlace(): ?int
    {
        return $this->place;
    }

    public function setPlace(?int $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Vehicle
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     * @return Vehicle
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function __toString()
    {
        return $this->matriculation . " - ". $this->brand . " " . $this->model;
    }
}
