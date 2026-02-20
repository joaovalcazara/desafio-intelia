<?php

namespace App\Controller; 
 
use App\Dto\CadastroDto;
use App\Service\CadastroService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CadastroController extends AbstractController
{
    public function __construct(
        private readonly CadastroService $cadastroService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {}

    #[Route('/api/cadastro', name: 'cadastro_salvar', methods: ['POST'])]
    public function salvar(Request $request): JsonResponse 
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), CadastroDto::class, 'json');
            
            $cadastro = $this->cadastroService->processarCadastro($dto);

            return $this->json([
                'status' => 'sucesso',
                'data' => [
                    'uuid' => $cadastro->getUuid()->toString(), 
                    'etapaAtual' => $cadastro->getEtapa(),
                    'message' => $dto->etapaAtual == 3 ? 'Cadastro completo!' : 'Etapa salva com sucesso!'
                ]
            ], JsonResponse::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'status' => 'erro',
                'message' => json_decode($e->getMessage(), true)
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'erro',
                'message' => 'Erro ao processar cadastro'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 

    #[Route('/api/cadastro/{uuid}', name: 'cadastro_buscar', methods: ['GET'])]
    public function buscar(string $uuid): JsonResponse
    {
        try {
            $dados = $this->cadastroService->buscarCadastro($uuid);

            if (!$dados) {
                return $this->json([
                    'status' => 'erro',
                    'message' => 'Cadastro não encontrado'
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return $this->json([
                'status' => 'sucesso',
                'data' => $dados
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'erro',
                'message' => 'Erro ao buscar cadastro'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    #[Route('/api/cadastro/{uuid}', name: 'cadastro_atualizar', methods: ['PUT'])]
    public function atualizar(string $uuid, Request $request): JsonResponse  
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), CadastroDto::class, 'json'); 

            $dto->uuid = $dto->uuid ?? $uuid;

            if (empty($dto->uuid)) {
                return $this->json([
                    'status' => 'erro',
                    'message' => 'UUID ausente para atualização'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $cadastro = $this->cadastroService->processarCadastro($dto);

            return $this->json([
                'status' => 'sucesso',
                'data' => [
                    'uuid' => $cadastro->getUuid()->toString(), 
                    'etapaAtual' => $cadastro->getEtapa(),
                    'message' => $dto->etapaAtual == 3 ? 'Cadastro completo!' : 'Etapa salva com sucesso!'
                ]
            ], JsonResponse::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'status' => 'erro',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'erro',
                'message' => 'Erro ao processar cadastro'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
