# desafio-intelia

# Backend – Cadastro Multi-Step (Symfony 7)

## Visão Geral
Este projeto implementa a base de um backend em Symfony 7 para um fluxo de
cadastro em três etapas, utilizando Docker, Doctrine ORM e MariaDB.

O objetivo é permitir que um usuário preencha seus dados de forma progressiva,
salvando cada etapa separadamente.

---

## Stack Utilizada
- PHP 8.3
- Symfony 7
- Doctrine ORM
- MariaDB
- Docker + Docker Compose

---

## Estrutura do Cadastro

O cadastro foi modelado como uma unica entidade (cadastro), visando simplicidade
e clareza, considerando o escopo do desafio.

### Etapas do Formulário

Passo 1  
- Nome completo
- Data de nascimento
- Email

Passo 2 – Endereço
- Rua
- Número
- CEP
- Cidade
- Estado

Passo 3 – Contato
- Telefone fixo
- Telefone celular

O campo step indica em qual etapa o cadastro se encontra. 
---

## Banco de Dados
- Integração com MariaDB via Doctrine
 
 