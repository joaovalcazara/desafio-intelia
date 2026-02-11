# desafio-intelia

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
- UUID gerado automaticamente
- Suporte a tipos Doctrine (Uuid, DateTimeImmutable)

### 2. Repository: CadastroRepository

**Arquivo**: [backend/app/src/Repository/CadastroRepository.php](backend/app/src/Repository/CadastroRepository.php)

Repository padrão do Doctrine estendendo `ServiceEntityRepository` com suporte à busca e manipulação de registros de cadastro.

### 3. Migração de Banco de Dados

**Arquivo**: [backend/app/migrations/Version20260211000301.php](backend/app/migrations/Version20260211000301.php)

Migração automática gerada pelo Doctrine que cria a tabela `cadastro` com todos os campos necessários.

### 4. Configuração Doctrine

**Arquivo**: [backend/app/config/packages/doctrine.yaml](backend/app/config/packages/doctrine.yaml)

Configurações do Doctrine ORM:
- Tipo UUID customizado
- Auto-mapping habilitado
- Suporte a lazy loading
- Estratégia de nomenclatura underscore_number_aware

### 5. Roteamento

**Arquivo**: [backend/app/config/routes.yaml](backend/app/config/routes.yaml)

Sistema de roteamento configurado para buscar atributos em controladores dentro de `src/Controller/`.

### 6. Configuração Nginx

**Arquivo**: [docker/nginx/backend.conf](docker/nginx/backend.conf)

- Raiz do projeto em `/var/www/public`
- Suporte a front controller pattern
- FastCGI passthrough para PHP-FPM
- Bloqueio de acesso a arquivos sensíveis (`.env`, `composer.json`, etc)

### 7. Dockerfile

**Arquivo**: [docker/backend/Dockerfile](docker/backend/Dockerfile)

Imagem PHP customizada com:
- Base: `php:8.3-fpm`
- Extensões compiladas: `intl`, `pdo`, `pdo_mysql`
- Composer instalado via COPY de imagem oficial
- Diretório de trabalho: `/var/www`

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

### Passos para Inicialização

1. **Clone o repositório**
   ```bash
   git clone <seu-repositorio>
   cd desafio-intelia
   ```

2. **Inicie os containers**
   ```bash
   docker-compose up -d
   ```

3. **Instale dependências PHP**
   ```bash
   docker-compose exec backend composer install
   ```

4. **Execute as migrações**
   ```bash
   docker-compose exec backend php bin/console doctrine:migrations:migrate
   ```

5. **Acesse a aplicação**
   ```
   http://localhost
   ```

---

## Estrutura de Resposta

A aplicação está pronta para implementar endpoints que:

1. **Criar novo cadastro** - Inicia o fluxo com etapa 1
2. **Atualizar etapa** - Permite atualizar dados de cada etapa
3. **Recuperar cadastro** - Busca cadastro pelo UUID
4. **Listar cadastros** - Retorna lista com paginação

---

## Tecnologias e Padrões

- **Padrão Clean Architecture**: Separação clara entre Entity, Repository e Controller
- **Dependency Injection**: Gerenciado automaticamente pelo Symfony
- **Doctrine ORM**: Abstração de banco de dados
- **API RESTful**: Pronta para implementação de endpoints JSON

---

## Próximos Passos para Desenvolvimento

1. Criar controladores (CadastroController)
2. Implementar endpoints RESTful:
   - `POST /cadastro` - Criar novo cadastro
   - `PUT /cadastro/{uuid}` - Atualizar cadastro
   - `GET /cadastro/{uuid}` - Recuperar cadastro
   - `GET /cadastros` - Listar cadastros
3. Adicionar validações com Symfony Validator
4. Implementar DTOs para transferência de dados
5. Adicionar testes unitários e de integração
6. Documentação da API com OpenAPI/Swagger

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

✅ **Concluído**:
- Estrutura do projeto Symfony 7 configurada
- Entity Cadastro com todos os campos
- Repository do Cadastro
- Migração de banco de dados
- Configuração Docker completa com Nginx, PHP-FPM e MariaDB
- Configuração Doctrine ORM

⏳ **Próximos**:
- Implementar controladores REST
- Adicionar validações de dados
- Testes automatizados
- Documentação da API

---

**Data de Atualização**: 11 de Fevereiro de 2026
 
 