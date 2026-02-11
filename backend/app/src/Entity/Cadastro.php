<?php

namespace App\Entity;

use App\Repository\CadastroRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CadastroRepository::class)]
class Cadastro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $etapa = null;

    #[ORM\Column(length: 255)]
    private ?string $nomeCompleto = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dataNascimento = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $rua = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $cep = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cidade = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $estado = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telefoneFixo = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telefoneCelular = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $uuid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtapa(): ?int
    {
        return $this->etapa;
    }

    public function setEtapa(int $etapa): static
    {
        $this->etapa = $etapa;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getNomeCompleto(): ?string
    {
        return $this->nomeCompleto;
    }

    public function setNomeCompleto(string $nomeCompleto): static
    {
        $this->nomeCompleto = $nomeCompleto;

        return $this;
    }

    public function getDataNascimento(): ?\DateTimeImmutable
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento(\DateTimeImmutable $dataNascimento): static
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRua(): ?string
    {
        return $this->rua;
    }

    public function setRua(string $rua): static
    {
        $this->rua = $rua;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCep(): ?string
    {
        return $this->cep;
    }

    public function setCep(?string $cep): static
    {
        $this->cep = $cep;

        return $this;
    }

    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    public function setCidade(?string $cidade): static
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getTelefoneFixo(): ?string
    {
        return $this->telefoneFixo;
    }

    public function setTelefoneFixo(?string $telefoneFixo): static
    {
        $this->telefoneFixo = $telefoneFixo;

        return $this;
    }

    public function getTelefoneCelular(): ?string
    {
        return $this->telefoneCelular;
    }

    public function setTelefoneCelular(?string $telefoneCelular): static
    {
        $this->telefoneCelular = $telefoneCelular;

        return $this;
    }
}
