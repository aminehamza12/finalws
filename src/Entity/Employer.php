<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmployerRepository")
 * uniqueConstraints={@ORM\UniqueConstraint(name="Employer_Nemp_unique",columns={"nemp"})}
 */
class Employer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message="Entrer un nom")
     * @Assert\Type("string",message="nom doit etre string")
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $Nom;

    /**
     * @Assert\NotBlank(message="Entrer un prenom")
     * @Assert\Type("string",message="prenom doit etre string")
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $Prenom;

    /**
     * @Assert\NotBlank(message="Entrer un nemp")
     * @Assert\Type("string",message="nemp doit etre string")
     * @ORM\Column(name="Nemp", type="integer")
     */
    private $Nemp;

    /**
     * @Assert\NotBlank(message="Entrer un job")
     * @Assert\Type("string",message="job doit etre string")
     * @ORM\Column(name="job", type="string", length=255)
     */
    private $Job;

    /**
     * @Assert\NotBlank(message="Entrer un salaire")
     * @Assert\Type("integer",message="le salaire doit etre entier")
     * @ORM\Column(name="salaire", type="integer")
     */
    private $Salaire;

    /**
     * @Assert\NotBlank(message="Entrer une date")
     * @Assert\Type("datetime",message="la date doit etre datetime")
     * @ORM\Column(name="date_rec", type="datetime")
     */
    private $Date_Rec;

    public function getId()
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): self
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getNemp(): ?int
    {
        return $this->Nemp;
    }

    public function setNemp(int $Nemp): self
    {
        $this->Nemp = $Nemp;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->Job;
    }

    public function setJob(string $Job): self
    {
        $this->Job = $Job;

        return $this;
    }

    public function getSalaire(): ?int
    {
        return $this->Salaire;
    }

    public function setSalaire(int $Salaire): self
    {
        $this->Salaire = $Salaire;

        return $this;
    }

    public function getDateRec(): ?\DateTimeInterface
    {
        return $this->Date_Rec;
    }

    public function setDateRec(\DateTimeInterface $Date_Rec): self
    {
        $this->Date_Rec = $Date_Rec;

        return $this;
    }
}
