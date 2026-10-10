
# Desafio Técnico — Cadastro de Pacientes

Aplicação web para cadastrar, consultar, editar e excluir pacientes vinculados a unidades de saúde.

## Tecnologias

- PHP
- Laravel
- MySQL
- HTML, CSS e JavaScript
- Git

## Funcionalidades

- Cadastro, listagem, consulta, edição e exclusão de pacientes.
- Busca parcial por nome.
- Associação de pacientes a unidades de saúde.
- Validação de campos obrigatórios, CPF único e data de nascimento.
- Mensagens de sucesso e erro na interface.
- Consultas SQL demonstrativas.

## Pré-requisitos

- PHP compatível com a versão do Laravel utilizada.
- Composer.
- MySQL.
- Git.

## Instalação

Clone o repositório e entre na pasta do projeto.

Instale as dependências:

```bash
composer install
```

Configure o arquivo de ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

Edite o `.env` e configure as variáveis de conexão com o MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

Crie o banco de dados antes de executar as migrations.

## Banco de dados

Execute:

```bash
php artisan migrate --seed
```

Esse comando cria as tabelas e cadastra as unidades de saúde iniciais.

## Executar a aplicação

Inicie o servidor:

```bash
php artisan serve
```

Abra no navegador:

http://127.0.0.1:8000

## API

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | /api/health-units | Listar unidades |
| GET | /api/patients | Listar pacientes |
| GET | /api/patients?search=Maria | Pesquisar pacientes |
| POST | /api/patients | Cadastrar paciente |
| GET | /api/patients/{patient} | Consultar paciente |
| PUT | /api/patients/{patient} | Editar paciente |
| DELETE | /api/patients/{patient} | Excluir paciente |

A API recebe e retorna JSON quando aplicável.

## Consultas SQL

As três consultas solicitadas no desafio estão no arquivo:

`sql/consultas.sql`

## Testes

Execute os testes automatizados com:

```bash
php artisan test
```

Configure um banco de testes separado, conforme necessário.

## Status da implementação

- [x] Migrations e seeder de unidades de saúde.
- [x] CRUD de pacientes.
- [x] Validações no backend.
- [x] Interface integrada à API.
- [x] Consultas SQL obrigatórias.
- [x] Teste automatizado de CPF duplicado.

## Git

O histórico de commits registra a implementação em etapas.