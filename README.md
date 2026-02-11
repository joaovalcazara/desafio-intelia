# desafio-intelia

## Execução

- Pré-requisitos:
  - `make` instalado na máquina
  - Docker Desktop instalado e em execução

- Inicialização:
  1. No diretório do projeto, execute:

     ```bash
     make setup
     ```

  2. Aguarde os containers e serviços inicializarem.
 
# Backend – Cadastro Multi-etapa (Symfony 7)

## Visão Geral
Este projeto implementa um backend completo em Symfony 7 para um fluxo de cadastro em três etapas, utilizando Docker, Doctrine ORM e MariaDB. O sistema permite que usuários preencham seus dados de forma progressiva, salvando cada etapa separadamente.

---

## Stack Utilizada
- **PHP**: 8.3
- **Framework**: Symfony 7.3
- **ORM**: Doctrine 3.6
- **Banco de Dados**: MariaDB 11.4
- **Containerização**: Docker + Docker Compose
- **Servidor Web**: Nginx 1.24

---

## Estrutura do Projeto

```
desafio-intelia/
├── backend/
│   ├── app/                          # Aplicação Symfony
│   │   ├── bin/
│   │   │   └── console              # CLI do Symfony
│   │   ├── config/
│   │   │   ├── bundles.php          # Bundles configurados
│   │   │   ├── services.yaml        # Definição de serviços
│   │   │   ├── routes.yaml          # Roteamento
│   │   │   ├── packages/            # Configurações específicas
│   │   │   │   ├── doctrine.yaml
│   │   │   │   ├── doctrine_migrations.yaml
│   │   │   │   └── framework.yaml
│   │   ├── migrations/
│   │   │   └── Version20260211000301.php  # Criação da tabela cadastro
│   │   ├── public/
│   │   │   └── index.php            # Entrypoint da aplicação
│   │   ├── src/
│   │   │   ├── Entity/
│   │   │   │   └── Cadastro.php     # Entidade de cadastro
│   │   │   ├── Repository/
│   │   │   │   └── CadastroRepository.php
│   │   │   └── Kernel.php           # Kernel do Symfony
│   │   ├── composer.json            # Dependências PHP
│   │   ├── .env                     # Variáveis de ambiente
│   │   └── .env.dev                 # Configurações de desenvolvimento
│   └── compose.yaml                 # Configuração Docker original
├── docker/
│   ├── backend/
│   │   └── Dockerfile              # Imagem PHP-FPM
│   └── nginx/
│       └── backend.conf            # Configuração Nginx
├── docker-compose.yml              # Orquestração dos containerss
└── README.md

```

---

## Estrutura do Cadastro

O cadastro foi modelado como uma única entidade (`Cadastro`) visando simplicidade e escalabilidade. O campo `etapa` indica em qual etapa (1, 2 ou 3) o cadastro se encontra.

### Etapas do Formulário

**Passo 1 - Dados Pessoais**
- Nome completo (`nomeCompleto`)
- Data de nascimento (`dataNascimento`)
- Email (`email`)

**Passo 2 - Endereço**
- Rua (`rua`)
- Número (`numero`)
- CEP (`cep`)
- Cidade (`cidade`)
- Estado (`estado`)

**Passo 3 - Contato**
- Telefone fixo (`telefoneFixo`)
- Telefone celular (`telefoneCelular`)

### Campos Adicionais
- `id`: Identificador único (auto-increment)
- `etapa`: Indicador da etapa atual (1, 2 ou 3)
- `uuid`: Identificador único universal para rastreamento

---

## Banco de Dados

### Integração com MariaDB

A aplicação utiliza Doctrine ORM para abstração de banco de dados com suporte a MariaDB. A tabela `cadastro` foi criada através de migração automática.

### Schema da Tabela `cadastro`

```sql
CREATE TABLE cadastro (
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    etapa INT NOT NULL,
    nome_completo VARCHAR(255) NOT NULL,
    data_nascimento DATETIME NOT NULL,
    email VARCHAR(255) NOT NULL,
    rua VARCHAR(255) NOT NULL,
    numero VARCHAR(255),
    cep VARCHAR(20),
    cidade VARCHAR(255),
    estado VARCHAR(20),
    telefone_fixo VARCHAR(20),
    telefone_celular VARCHAR(20),
    uuid BINARY(16) NOT NULL UNIQUE,
    INDEX UNIQ_CBC68492D17F50A6 (uuid)
) DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## Infraestrutura Docker

### Serviços

#### 1. **Nginx** (proxy)
- Imagem: `nginx:1.24`
- Container: `nginx`
- Porta: `80:80`
- Função: Proxy reverso que roteia as requisições para o backend

#### 2. **Backend** (PHP-FPM)
- Dockerfile customizado baseado em `php:8.3-fpm`
- Container: `symfony_api`
- Função: Executa a aplicação Symfony
- Extensões PHP instaladas:
  - `intl` (internacionalização)
  - `pdo` e `pdo_mysql` (acesso a banco de dados)
  - `zip` (manipulação de arquivos)
- Composer instalado globalmente

#### 3. **MariaDB**
- Imagem: `mariadb:11.4`
- Container: `mariadb`
- Porta: `3306:3306`
- Credenciais:
  - Usuário: `intelia`
  - Senha: `intelia`
  - Database: `intelia`
- Root password: `root`

### Configuração de Rede

Todos os serviços estão conectados à rede `api-network` para comunicação interna. O Nginx é o ponto de entrada, redirecionando requisições para o backend, que por sua vez se conecta ao MariaDB.

---

## Componentes Implementados

### 1. Entity: Cadastro

**Arquivo**: [backend/app/src/Entity/Cadastro.php](backend/app/src/Entity/Cadastro.php)

Entidade Doctrine que mapeia a tabela `cadastro` com todos os campos necessários para o formulário em 3 etapas.

Propriedades:
- Getters e setters para todos os campos
- UUID gerado automaticamente (Uuid v4)
- Suporte a tipos Doctrine (Uuid, DateTimeImmutable)
- Campos obrigatórios na etapa 1: nome, email, data de nascimento
- Campos opcionais nas etapas 2 e 3

### 2. DTO: CadastroDto

**Arquivo**: [backend/app/src/Dto/CadastroDto.php](backend/app/src/Dto/CadastroDto.php)

Data Transfer Object com validações Symfony Validator:

**Etapa 1 - Dados Pessoais**:
- `nomeCompleto` - NotBlank obrigatório
- `email` - NotBlank + Email válido obrigatório
- `dataNascimento` - NotBlank obrigatório

**Etapa 2 - Endereço**:
- `rua` - NotBlank obrigatório
- `numero` - NotBlank obrigatório
- `cep` - NotBlank + Regex (99999-999) obrigatório
- `cidade` - NotBlank obrigatório
- `estado` - NotBlank obrigatório

**Etapa 3 - Contato**:
- `telefoneCelular` - NotBlank + Regex (99) 99999-9999 obrigatório
- `telefoneFixo` - Regex (99) 9999-9999 opcional

Campos adicionais:
- `etapaAtual` - Indica qual etapa está sendo enviada (obrigatório)
- `uuid` - Identificador para atualizar cadastro existente (opcional)

### 3. Controller: CadastroController

**Arquivo**: [backend/app/src/Controller/CadastroController.php](backend/app/src/Controller/CadastroController.php)

Controlador REST que implementa os endpoints:

#### POST /api/cadastro
- Cria novo cadastro ou atualiza existente (se uuid fornecido)
- Valida dados conforme a etapa enviada
- Previne pulo de etapas (sequência obrigatória)
- Novos cadastros obrigatoriamente começam em etapa 1
- Retorna UUID e etapa atual em caso de sucesso

#### GET /api/cadastro/{uuid}
- Busca cadastro pelo UUID
- Retorna todos os dados do cadastro em JSON
- Retorna erro 404 se não encontrado

Funcionalidades:
- Validação por grupos (etapa1, etapa2, etapa3)
- Mapeamento automático DTO → Entity
- Persistência no banco via Doctrine
- Respostas JSON padronizadas com status

### 4. Repository: CadastroRepository

**Arquivo**: [backend/app/src/Repository/CadastroRepository.php](backend/app/src/Repository/CadastroRepository.php)

Repository padrão do Doctrine estendendo `ServiceEntityRepository` com suporte à busca e manipulação de registros de cadastro.

### 5. Migrações de Banco de Dados

**Arquivos**: 
- [backend/app/migrations/Version20260211000301.php](backend/app/migrations/Version20260211000301.php)
- [backend/app/migrations/Version20260211135746.php](backend/app/migrations/Version20260211135746.php)
- [backend/app/migrations/Version20260211152925.php](backend/app/migrations/Version20260211152925.php)

Migrações automáticas geradas pelo Doctrine que criam a tabela `cadastro` com todos os campos necessários.

### 6. Configuração Doctrine

**Arquivo**: [backend/app/config/packages/doctrine.yaml](backend/app/config/packages/doctrine.yaml)

Configurações do Doctrine ORM:
- Tipo UUID customizado
- Auto-mapping habilitado
- Suporte a lazy loading
- Estratégia de nomenclatura underscore_number_aware

### 7. Roteamento

**Arquivo**: [backend/app/config/routes.yaml](backend/app/config/routes.yaml)

Sistema de roteamento configurado para buscar atributos em controladores dentro de `src/Controller/`.

### 8. Configuração Nginx

**Arquivo**: [docker/nginx/backend.conf](docker/nginx/backend.conf)

- Raiz do projeto em `/var/www/public`
- Suporte a front controller pattern
- FastCGI passthrough para PHP-FPM
- Bloqueio de acesso a arquivos sensíveis (`.env`, `composer.json`, etc)

### 9. Dockerfile

**Arquivo**: [docker/backend/Dockerfile](docker/backend/Dockerfile)

Imagem PHP customizada com:
- Base: `php:8.3-fpm`
- Extensões compiladas: `intl`, `pdo`, `pdo_mysql`
- Composer instalado via COPY de imagem oficial
- Diretório de trabalho: `/var/www`

### 10. Docker Compose

**Arquivo**: [docker-compose.yml](docker-compose.yml)

Orquestração completa com:
- Nginx 1.24 como proxy reverso
- PHP-FPM 8.3 com Symfony
- MariaDB 11.4 para persistência
- Rede interna `api-network`
- Volume persistente para banco de dados

---

## Configuração de Ambiente

### Variáveis de Desenvolvimento

**Arquivo**: [backend/app/.env.dev](backend/app/.env.dev)

```env
APP_SECRET=79982dfc045224a3e49264de765c15f9
DATABASE_URL=mysql://intelia:intelia@db:3306/intelia?serverVersion=mariadb-11.4.0
```

### Configurações Principal

**Arquivo**: [backend/app/.env](backend/app/.env)

- `APP_ENV=dev`
- `DEFAULT_URI=http://localhost`

---

## Dependências

### Dependências Principais (composer.json)

- **symfony/framework-bundle**: Framework web
- **symfony/console**: CLI
- **symfony/dotenv**: Gerenciamento de variáveis de ambiente
- **symfony/flex**: Package manager do Symfony
- **symfony/runtime**: Runtime environment
- **symfony/serializer**: Serialização de dados
- **symfony/validator**: Validação de dados
- **symfony/yaml**: Parseamento YAML
- **doctrine/doctrine-bundle**: ORM integration
- **doctrine/doctrine-migrations-bundle**: Gerenciamento de migrações
- **doctrine/orm**: ORM framework

### Dependências de Desenvolvimento

- **symfony/maker-bundle**: Geração de código através de commands

---

## Como Executar

### Pré-requisitos

- Docker e Docker Compose instalados
- Git (para versionamento)
- Windows, macOS ou Linux

### Passos para Inicialização

#### 1. Clone ou baixe o repositório
```bash
git clone <seu-repositorio>
cd desafio-intelia
```

#### 2. Inicie todos os containers com Docker Compose
```bash
docker-compose up -d
```

Este comando irá:
- Construir a imagem PHP-FPM customizada
- Baixar as imagens do Nginx e MariaDB
- Criar os containers e volume de persistência
- Conectar os containers à rede `api-network`
- Iniciar todos os serviços em background

**Status esperado:**
```
✓ Container nginx    Started
✓ Container mariadb  Started
✓ Container symfony_api  Started
```

#### 3. Instale as dependências PHP
```bash
docker-compose exec backend composer install
```

Isto irá:
- Instalar todos os pacotes Symfony e dependências listadas no `composer.json`
- Gerar o autoloader do Composer
- Preparar os binários do projeto

#### 4. Execute as migrações do banco de dados
```bash
docker-compose exec backend php bin/console doctrine:migrations:migrate
```

Isto irá:
- Executar todas as migrações pendentes
- Criar a tabela `cadastro` com todos os campos
- Preparar o banco para receber dados

#### 5. Acesse a aplicação
```
http://localhost
```

A API está pronta para receber requisições!

### Verificar Status dos Containers
```bash
docker-compose ps
```

Saída esperada:
```
NAME            COMMAND                   SERVICE   STATUS         PORTS
mariadb         "docker-entrypoint.s…"  db        Up             3306/3306
nginx           "/docker-entrypoint.…"  proxy     Up             0.0.0.0:80->80/tcp
symfony_api     "docker-php-entrypoi…"  backend   Up
```

### Parar os Containers
```bash
docker-compose down
```

Use `-v` para remover volumes também (cuidado: irá deletar dados do banco):
```bash
docker-compose down -v
```

### Ver Logs
Para ver os logs de todos os serviços:
```bash
docker-compose logs -f
```

Para ver logs de um serviço específico:
```bash
docker-compose logs -f backend   # Symfony
docker-compose logs -f proxy      # Nginx
docker-compose logs -f db         # MariaDB
```

---

## Testando os Endpoints

### 1. Criar Cadastro - Etapa 1

**POST** `http://localhost/api/cadastro`

```json
{
  "etapaAtual": 1,
  "nomeCompleto": "João Silva",
  "email": "joao@example.com",
  "dataNascimento": "1990-05-15"
}
```

**Resposta (201)**:
```json
{
  "status": "sucesso",
  "data": {
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "etapaAtual": 1,
    "message": "Etapa salva com sucesso!"
  }
}
```

### 2. Continuar Cadastro - Etapa 2

**POST** `http://localhost/api/cadastro`

```json
{
  "etapaAtual": 2,
  "uuid": "550e8400-e29b-41d4-a716-446655440000",
  "rua": "Rua das Flores",
  "numero": "123",
  "cep": "12345-678",
  "cidade": "São Paulo",
  "estado": "SP"
}
```

**Resposta (200)**:
```json
{
  "status": "sucesso",
  "data": {
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "etapaAtual": 2,
    "message": "Etapa salva com sucesso!"
  }
}
```

### 3. Finalizar Cadastro - Etapa 3

**POST** `http://localhost/api/cadastro`

```json
{
  "etapaAtual": 3,
  "uuid": "550e8400-e29b-41d4-a716-446655440000",
  "telefoneCelular": "(11) 98765-4321",
  "telefoneFixo": "(11) 3456-7890"
}
```

**Resposta (200)**:
```json
{
  "status": "sucesso",
  "data": {
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "etapaAtual": 3,
    "message": "Cadastro completo!"
  }
}
```

### 4. Recuperar Cadastro Completo

**GET** `http://localhost/api/cadastro/550e8400-e29b-41d4-a716-446655440000`

**Resposta (200)**:
```json
{
  "status": "sucesso",
  "data": {
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "etapaAtual": 3,
    "nomeCompleto": "João Silva",
    "email": "joao@example.com",
    "dataNascimento": "1990-05-15",
    "rua": "Rua das Flores",
    "numero": "123",
    "cep": "12345-678",
    "cidade": "São Paulo",
    "estado": "SP",
    "telefoneCelular": "(11) 98765-4321",
    "telefoneFixo": "(11) 3456-7890"
  }
}
```

### Testes com cURL

```bash
# Criar etapa 1
curl -X POST http://localhost/api/cadastro \
  -H "Content-Type: application/json" \
  -d '{"etapaAtual":1,"nomeCompleto":"João","email":"joao@test.com","dataNascimento":"1990-01-01"}'

# Buscar cadastro
curl http://localhost/api/cadastro/550e8400-e29b-41d4-a716-446655440000
```

---

## Funcionalidades Completas

A aplicação implementa uma API REST completa com os seguintes endpoints:

### ✅ POST /api/cadastro
- **Descrição**: Cria novo cadastro ou atualiza existente
- **Validações**: Por grupo (etapa1, etapa2, etapa3)
- **Controle de Sequência**: Impede pulo de etapas
- **Resposta**: UUID do cadastro e etapa atual

### ✅ GET /api/cadastro/{uuid}
- **Descrição**: Recupera cadastro completo pelo UUID
- **Validação**: Retorna 404 se não encontrado
- **Resposta**: Todos os dados preenchidos do cadastro

---

## Tecnologias e Padrões

- **Padrão Clean Architecture**: Separação clara entre Entity, DTO, Repository e Controller
- **Dependency Injection**: Gerenciado automaticamente pelo Symfony
- **Doctrine ORM**: Abstração de banco de dados com migrations
- **API RESTful**: Endpoints JSON com tratamento de erros
- **Validação em Camadas**: Validações por grupo com Symfony Validator
- **Containerização Completa**: Docker Compose com 3 serviços integrados
- **Controle de Fluxo**: Sequência obrigatória de etapas com UUID tracking

---

## Próximos Passos para Desenvolvimento

1. Implementar endpoint GET `/api/cadastros` - Listar cadastros com paginação
2. Adicionar filtros avançados na listagem
3. Implementar autenticação e autorização
4. Adicionar logs estruturados
5. Testes automatizados (unit e integration)
6. Documentação da API com Swagger/OpenAPI
7. Rate limiting
8. CORS configuration
9. Cache de consultas

---

## Estrutura de Pastas Explicada

- **backend/app/**: Código da aplicação Symfony
- **backend/app/src/**: Código fonte (Entities, Repositories, Controllers)
- **backend/app/config/**: Configurações da aplicação
- **backend/app/public/**: Documentos públicos (index.php)
- **docker/**: Dockerfiles e configurações de containers
- **docker-compose.yml**: Orquestração dos serviços

---

## Documentação Oficial

- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [Doctrine ORM](https://www.doctrine-project.org/index.html)
- [Docker Documentation](https://docs.docker.com/)
- [MariaDB Documentation](https://mariadb.com/kb/en/documentation/)

---

## Status do Projeto

✅ **Concluído - Fully Operational**:
- Estrutura do projeto Symfony 7 configurada
- Entity Cadastro com todos os campos
- DTO com validações por grupo (etapa 1, 2, 3)
- Repository do Cadastro
- Migrações de banco de dados
- Configuração Docker completa com Nginx, PHP-FPM e MariaDB
- Configuração Doctrine ORM
- **Controller REST com endpoints implementados:**
  - ✅ POST /api/cadastro (Criar/Atualizar cadastro)
  - ✅ GET /api/cadastro/{uuid} (Recuperar cadastro)
- **Validações implementadas:**
  - ✅ Validação de email
  - ✅ Validação de formato de CEP (00000-000)
  - ✅ Validação de formato de celular ((00) 00000-0000)
  - ✅ Validação de formato de telefone fixo ((00) 0000-0000)
  - ✅ Controle de sequência de etapas
  - ✅ Obrigatoriedade por etapa

⏳ **Possíveis Melhorias Futuras**:
- Implementar endpoint GET /api/cadastros (listagem com paginação)
- Adicionar filtros avançados na listagem
- Implementar autenticação e autorização
- Adicionar logs estruturados
- Testes automatizados (unit e integration)
- Documentação da API com Swagger/OpenAPI
- Rate limiting
- CORS configuration
- Cache de consultas

---

**Data de Atualização**: 11 de Fevereiro de 2026

### Sumário da Implementação Completa

Nesta versão o projeto foi desenvolvido com:

✅ **Backend Symfony 7 completo e funcional**
- 2 endpoints REST implementados e testados
- Validação de dados em 3 etapas diferentes
- Controle de sequência de formulário
- Persistência em MariaDB via Doctrine ORM
- Resposta estruturada em JSON

✅ **Infraestrutura Docker pronta para produção**
- Nginx como proxy reverso
- PHP-FPM 8.3 com extensões compiladas
- MariaDB com volume persistente
- Rede interna isolada
- Fácil deployment em qualquer máquina com Docker

✅ **Código limpo e escalável**
- Arquitetura Clean Code
- Validações pelo grupo do validator
- DTOs para transferência de dados
- Repository pattern para abstração do banco
- Dependency injection automático

✅ **Documentação completa**
- README atualizado com tudo implementado
- Guia passo a passo para rodar com Docker
- Exemplos de requisições para todos os endpoints
- Instruções de troubleshooting e logs
 
 