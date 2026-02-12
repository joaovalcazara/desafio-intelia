
Projeto — Desafio Intelia
=========================

Este repositório contém uma API em Symfony (backend) e uma aplicação frontend em Vue (Vite). Abaixo estão instruções rápidas para rodar o projeto, a estrutura do repositório e detalhes do banco de dados usado no ambiente Docker.

**Requisitos**
- **Docker**: instalado e em execução (Docker Desktop ou Docker Engine).
- **Make**: instalado (a execução do setup é feita via `make` em um shell Bash).
- **Bash / WSL2 (Windows)**: em Windows recomendo usar WSL2, Git Bash ou terminal que suporte comandos Make e Bash.

**Setup inicial (executar em Bash)**
1. Abra um terminal Bash no diretório do projeto.
2. Rode o comando de setup (o `make setup` já sobe os containers via `docker-compose up -d`):

```
make setup
```

3. (Opcional) Verifique logs:

```
docker-compose logs -f
```

**Resumo rápido de execução**
- Em Bash: `make setup` (já sobe os containers via `docker-compose up -d`)

**Estrutura do projeto**
- **backend/**: aplicação Symfony
	- `backend/app/public` — ponto de entrada público
	- `backend/app/src` — código fonte (Controllers, Service, Entity, Repository)
	- `backend/app/config` — configurações do Symfony
	- `backend/app/migrations` — migrations do Doctrine
- **frontend/**: aplicação Vite + Vue
	- `frontend/src` — componentes e views
	- `frontend/public` — assets públicos
- **docker/**: Dockerfiles e configurações para containers
	- `docker/backend/Dockerfile`
	- `docker/frontend/Dockerfile`
	- `docker/nginx/www.conf` e `docker/nginx/opcache.ini`
- **nginx/**: configuração do nginx utilizada pelo container proxy
- `docker-compose.yml` — orquestração dos serviços (proxy, backend, db, frontend)
- `Makefile` — atalhos para setup/deploy/local

**Banco de dados (ambiente Docker)**
- Imagem: `mariadb:11.4`
- Banco: `intelia`
- Usuário: `intelia` / Senha: `intelia`
- Root: `root`
- Porta mapeada: `3306:3306`
- Volume persistente: `database_data` (definido em `docker-compose.yml`)

Exemplo de comando para rodar migrations (dentro do serviço backend):

```
docker-compose exec backend php bin/console doctrine:migrations:migrate
```


**Tecnologias utilizadas**
- Docker & Docker Compose — orquestração dos containers
- Make — scripts de setup/atalhos no `Makefile`
- PHP — linguagem do backend
- Composer — gerenciador de dependências PHP
- Symfony — framework PHP usado no backend
- Doctrine ORM & Doctrine Migrations — mapeamento objeto-relacional e migrations
- MariaDB — banco de dados (via container)
- Nginx — reverse proxy usado no container `proxy`
- Node.js & npm/yarn — runtime e gerenciador de pacotes do frontend
- Vite — bundler / dev server do frontend
- Vue.js — framework do frontend
- Git — controle de versão
 
 

**Schema do banco de dados**

A tabela principal utilizada pela aplicação é `cadastro`. Abaixo está o schema SQL gerado pelas migrations (compatível com MariaDB/MySQL):

```sql
CREATE TABLE cadastro (
	id INT AUTO_INCREMENT NOT NULL,
	etapa INT NOT NULL,
	nome_completo VARCHAR(255) NOT NULL,
	data_nascimento DATETIME NOT NULL,
	email VARCHAR(255) NOT NULL,
	rua VARCHAR(255) DEFAULT NULL,
	numero VARCHAR(255) DEFAULT NULL,
	cep VARCHAR(20) DEFAULT NULL,
	cidade VARCHAR(255) DEFAULT NULL,
	estado VARCHAR(20) DEFAULT NULL,
	telefone_fixo VARCHAR(20) DEFAULT NULL,
	telefone_celular VARCHAR(20) DEFAULT NULL,
	uuid BINARY(16) NOT NULL,
	UNIQUE INDEX UNIQ_CBC68492D17F50A6 (uuid),
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`;
```

Descrição das colunas:
- `id`: chave primária auto-increment.
- `etapa`: inteiro representando a etapa atual do cadastro.
- `nome_completo`: nome completo do usuário.
- `data_nascimento`: data de nascimento (datetime).
- `email`: endereço de e-mail.
- `rua`, `numero`, `cep`, `cidade`, `estado`: campos de endereço (alguns são opcionais).
- `telefone_fixo`, `telefone_celular`: telefones (opcionais).
- `uuid`: UUID armazenado em formato binário (`BINARY(16)`), com índice único usado para identificação pública.

Observações:
- O `uuid` é gerado pela aplicação (Symfony) usando `Uuid::v4()` e mapeado para `BINARY(16)` no banco.
- As migrations na pasta `backend/app/migrations` contêm o histórico de alterações do schema.

**Notas**
- Se estiver no Windows e não tiver um terminal Bash funcional, use WSL2 ou Git Bash para garantir que o `make setup` rode corretamente.
- Verifique `docker-compose.yml` se precisar ajustar credenciais ou portas.

**APIs**

A aplicação expõe os seguintes endpoints REST para gerenciar o fluxo de cadastro:

- Base: `http://localhost/api/cadastro` (quando executado via proxy Nginx do `docker-compose`)

- `POST /api/cadastro`
	- Descrição: cria ou atualiza um cadastro parcial por etapa. Para atualizações, envie o campo `uuid` retornado anteriormente.
	- Payload (JSON): campos usados em cada etapa (validações aplicadas por etapa):
		- `etapaAtual` (int, obrigatório) — 1, 2 ou 3
		- Etapa 1: `nomeCompleto` (string, obrigatório), `email` (string, e-mail), `dataNascimento` (YYYY-MM-DD)
		- Etapa 2: `rua`, `numero`, `cep` (formato 00000-000), `cidade`, `estado` (todos obrigatórios nesta etapa)
		- Etapa 3: `telefoneCelular` (formato (00) 00000-0000, obrigatório), `telefoneFixo` (opcional, formato (00) 0000-0000)
		- `uuid` (string, opcional) — UUID para atualizar um cadastro existente

	- Exemplo (etapa 1):

```json
{
	"etapaAtual": 1,
	"nomeCompleto": "João da Silva",
	"email": "joao@example.com",
	"dataNascimento": "1990-05-20"
}
```

	- Exemplo curl:

```bash
curl -X POST http://localhost/api/cadastro \
	-H "Content-Type: application/json" \
	-d '{"etapaAtual":1,"nomeCompleto":"João","email":"joao@example.com","dataNascimento":"1990-05-20"}'
```

	- Resposta de sucesso (HTTP 200):

```json
{
	"status": "sucesso",
	"data": {
		"uuid": "<uuid-gerado>",
		"etapaAtual": 1,
		"message": "Etapa salva com sucesso!"
	}
}
```

	- Erros:
		- HTTP 400: dados inválidos (mensagem com motivo)
		- HTTP 500: erro interno ao processar

- `GET /api/cadastro/{uuid}`
	- Descrição: retorna os dados completos do cadastro identificado por `uuid`.
	- Exemplo curl:

```bash
curl http://localhost/api/cadastro/<uuid>
```

	- Resposta de sucesso (HTTP 200):

```json
{
	"status": "sucesso",
	"data": {
		"uuid": "<uuid>",
		"etapaAtual": 3,
		"nomeCompleto": "João da Silva",
		"email": "joao@example.com",
		"dataNascimento": "1990-05-20",
		"rua": "Rua A",
		"numero": "123",
		"cep": "00000-000",
		"cidade": "Cidade",
		"estado": "SP",
		"telefoneCelular": "(11) 90000-0000",
		"telefoneFixo": "(11) 3000-0000"
	}
}
```

	- Erros:
		- HTTP 404: cadastro não encontrado
		- HTTP 500: erro interno ao buscar

Validações e comportamento adicionais:
- As validações por etapa seguem as constraints definidas em `backend/app/src/Dto/CadastroDto.php`.
- A sequência de etapas é verificada — não é permitido pular etapas (por exemplo, ir direto de 1 para 3).
- O `uuid` público é retornado em respostas de criação/atualização e deve ser usado para recuperar/atualizar o cadastro.


