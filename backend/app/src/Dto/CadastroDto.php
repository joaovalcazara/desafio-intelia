<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CadastroDto
{
    #[Assert\NotBlank(groups: ['etapa1'])]
    public ?string $nomeCompleto = null;

    #[Assert\NotBlank(groups: ['etapa1'])]
    #[Assert\Email(message: 'E-mail inválido', groups: ['etapa1'])]
    public ?string $email = null;

    #[Assert\NotBlank(groups: ['etapa1'])]
    public ?string $dataNascimento = null;

    #[Assert\NotBlank(groups: ['etapa2'])]
    public ?string $rua = null;

    #[Assert\NotBlank(groups: ['etapa2'])]
    public ?string $numero = null;

    #[Assert\NotBlank(groups: ['etapa2'])]
    #[Assert\Regex(
        pattern: '/^\d{5}-\d{3}$/',
        message: 'O CEP deve estar no formato 00000-000',
        groups: ['etapa2']
    )]
    public ?string $cep = null;

    #[Assert\NotBlank(groups: ['etapa2'])]
    public ?string $cidade = null;

    #[Assert\NotBlank(groups: ['etapa2'])]
    public ?string $estado = null;

    #[Assert\NotBlank(groups: ['etapa3'])]
    #[Assert\Regex(
        pattern: '/^\(\d{2}\) \d{5}-\d{4}$/',
        message: 'O celular deve estar no formato (00) 00000-0000',
        groups: ['etapa3']
    )]
    public ?string $telefoneCelular = null;

    #[Assert\Regex(
        pattern: '/^\(\d{2}\) \d{4}-\d{4}$/',
        message: 'O telefone fixo deve estar no formato (00) 0000-0000',
        groups: ['etapa3']
    )]
    public ?string $telefoneFixo = null;

    #[Assert\NotBlank]
    public ?int $etapaAtual = null;

    public ?string $uuid = null;
}