<?php

namespace App\Controller; 
 
use App\Dto\CadastroDto;
use App\Entity\Cadastro;
use App\Repository\CadastroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CadastroController extends AbstractController
{
    #[Route('/api/cadastro', name: 'cadastro_salvar', methods: ['POST'])]
    public function salvar(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        CadastroRepository $cadastroRepository
    ): JsonResponse 
    {
        $dto = $serializer->deserialize($request->getContent(), CadastroDto::class, 'json');

        $gruposValidacao = match ($dto->etapaAtual) {
            1 => ['etapa1'],
            2 => ['etapa2'],
            3 => ['etapa3'],
            default => [],
        };

        $erros = $validator->validate($dto, null, $gruposValidacao);
        if (count($erros) > 0) {
            return $this->json([
                'status' => 'erro',
                'erros' => $erros
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        //Verifica se ja existe um cadastro com o uuid fornecido, se sim, atualiza, se não, cria um novo cadastro
        $cadastro = $dto->uuid ? $cadastroRepository->findOneBy(['uuid' => $dto->uuid]) : new Cadastro();

        if ($dto->uuid && !$cadastro) {
            return $this->json([
                'status' => 'erro',
                'message' => 'Cadastro não encontrado para atualização'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // Impede do usuário pular etapas ou começar em uma etapa diferente da 1
        if ($cadastro->getId() !== null) {
            if ($dto->etapaAtual > ($cadastro->getEtapa() + 1)) {
                return $this->json([
                    'status' => 'erro',
                    'message' => sprintf(
                        'Sequência inválida. Você parou na etapa %d e tentou ir para a etapa %d.',
                        $cadastro->getEtapa(),
                        $dto->etapaAtual
                    )
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        } elseif ($dto->etapaAtual !== 1) {
            return $this->json([
                'status' => 'erro',
                'message' => 'Novos cadastros devem começar na Etapa 1.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->mapearEntity($cadastro, $dto);

        $entityManager->persist($cadastro);
        $entityManager->flush();

        return $this->json([
            'status' => 'sucesso',
            'data' => [
                'uuid' => $cadastro->getUuid()->toString(), 
                'etapaAtual' => $cadastro->getEtapa(),
                'message' => $dto->etapaAtual == 3 ? 'Cadastro completo!' : 'Etapa salva com sucesso!'
            ]
        ], JsonResponse::HTTP_OK);
    }
 

   private function mapearEntity(Cadastro $entity, CadastroDto $dto): void
    {
        if ($dto->nomeCompleto !== null) {
            $entity->setNomeCompleto($dto->nomeCompleto);
        }
        if ($dto->email !== null) {
            $entity->setEmail($dto->email);
        }
        if ($dto->dataNascimento !== null) {
            $entity->setDataNascimento(new \DateTimeImmutable($dto->dataNascimento));
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

    #[Route('/api/cadastro/{uuid}', name: 'cadastro_buscar', methods: ['GET'])]
    public function buscar(string $uuid, CadastroRepository $cadastroRepository): JsonResponse
    {
        $cadastro = $cadastroRepository->findOneBy(['uuid' => $uuid]);

        if (!$cadastro) {
            return $this->json([
                'status' => 'erro',
                'message' => 'Cadastro não encontrado'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json([
            'status' => 'sucesso',
            'data' => [
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
            ]
        ], JsonResponse::HTTP_OK);
    }
}
