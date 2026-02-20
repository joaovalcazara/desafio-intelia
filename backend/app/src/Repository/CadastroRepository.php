<?php

namespace App\Repository;

use App\Entity\Cadastro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cadastro>
 */
class CadastroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cadastro::class);
    }

    public function buscarPorUuid(string $uuid): ?Cadastro
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    public function salvar(Cadastro $cadastro): void
    {
        $this->getEntityManager()->persist($cadastro);
        $this->getEntityManager()->flush();
    }
}
