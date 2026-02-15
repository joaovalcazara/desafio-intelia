<?php

namespace App\Service;

use App\Dto\CadastroDto;
use App\Entity\Cadastro;
use App\Repository\CadastroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CadastroService
{
    public function __construct(
        private readonly CadastroRepository $cadastroRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator
    ) {}

 
    public function validarPorEtapa(CadastroDto $dto): array
    {
        $gruposValidacao = match ($dto->etapaAtual) {
            1 => ['etapa1'],
            2 => ['etapa2'],
            3 => ['etapa3'],
            default => [],
        };

        $erros = $this->validator->validate($dto, null, $gruposValidacao);
        
        return $erros->count() > 0 ? iterator_to_array($erros) : [];
    }

 
    public function obterOuCriarCadastro(?string $uuid): Cadastro
    {
        if ($uuid) {
            $cadastro = $this->cadastroRepository->findOneBy(['uuid' => $uuid]);
            if (!$cadastro) {
                throw new \InvalidArgumentException('Cadastro não encontrado para atualização');
            }
            return $cadastro;
        }

        return new Cadastro();
    }

 
    public function validarSequenciaEtapas(Cadastro $cadastro, int $etapaAtual): void
    {
        if ($cadastro->getId() !== null) {
            if ($etapaAtual > ($cadastro->getEtapa() + 1)) {
                throw new \InvalidArgumentException(sprintf(
                    'Sequência inválida. Você parou na etapa %d e tentou ir para a etapa %d.',
                    $cadastro->getEtapa(),
                    $etapaAtual
                ));
            }
        } else {
            if ($etapaAtual !== 1) {
                throw new \InvalidArgumentException('Novos cadastros devem começar na Etapa 1.');
            }
        }
    }


    public function mapearEntity(Cadastro $entity, CadastroDto $dto): void
    {
        if ($dto->etapaAtual >= 1) {
            if ($dto->nomeCompleto !== null) {
                $entity->setNomeCompleto($dto->nomeCompleto);
            }
            if ($dto->email !== null) {
                $entity->setEmail($dto->email);
            }
            if ($dto->dataNascimento !== null) {
                $entity->setDataNascimento(new \DateTimeImmutable($dto->dataNascimento));
            }
        }

        if ($dto->etapaAtual >= 2) {
            $entity->setRua($dto->rua ?? $entity->getRua());
            $entity->setNumero($dto->numero ?? $entity->getNumero());
            $entity->setCep($dto->cep ?? $entity->getCep());
            $entity->setCidade($dto->cidade ?? $entity->getCidade());
            $entity->setEstado($dto->estado ?? $entity->getEstado());
        }

        if ($dto->etapaAtual >= 3) {
            $entity->setTelefoneCelular($dto->telefoneCelular ?? $entity->getTelefoneCelular());
            $entity->setTelefoneFixo($dto->telefoneFixo ?? $entity->getTelefoneFixo());
        }

        $entity->setEtapa($dto->etapaAtual);
    }


    public function salvarCadastro(Cadastro $cadastro): Cadastro
    {
        $this->entityManager->persist($cadastro);
        $this->entityManager->flush();

        return $cadastro;
    }


    public function processarCadastro(CadastroDto $dto): Cadastro
    {
        $erros = $this->validarPorEtapa($dto);

        if (count($erros) > 0) {

            $retorno = [];

            foreach ($erros as $erro) {
                $retorno[] = [
                    'campo' => $erro->getPropertyPath(),
                    'mensagem' => $erro->getMessage(),
                ];
            }

            throw new \InvalidArgumentException(
                json_encode($retorno, JSON_UNESCAPED_UNICODE)
            );
        }

        $cadastro = $this->obterOuCriarCadastro($dto->uuid);

        $this->validarSequenciaEtapas($cadastro, $dto->etapaAtual);

        $this->mapearEntity($cadastro, $dto);

        return $this->salvarCadastro($cadastro);
    }



    public function buscarCadastro(string $uuid): ?array
    {
        $cadastro = $this->cadastroRepository->findOneBy(['uuid' => $uuid]);

        if (!$cadastro) {
            return null;
        }

        return [
            'uuid' => $cadastro->getUuid(),
            'etapaAtual' => $cadastro->getEtapa(),
            'nomeCompleto' => $cadastro->getNomeCompleto(),
            'email' => $cadastro->getEmail(),
            'dataNascimento' => $cadastro->getDataNascimento()->format('Y-m-d'),
            'rua' => $cadastro->getRua(),
            'numero' => $cadastro->getNumero(),
            'cep' => $cadastro->getCep(),
            'cidade' => $cadastro->getCidade(),
            'estado' => $cadastro->getEstado(),
            'telefoneCelular' => $cadastro->getTelefoneCelular(),
            'telefoneFixo' => $cadastro->getTelefoneFixo()
        ];
    }
}
