
# Desafio Técnico — Cadastro de Pacientes

Aplicação web para cadastrar e consultar pacientes vinculados a unidades de saúde. O projeto foi desenvolvido com Laravel, MySQL e JavaScript puro, conforme os requisitos do desafio técnico.

## Tecnologias

- PHP 8.3
- Laravel 13
- MySQL
- HTML, CSS e JavaScript
- PHPUnit
- Git

## Funcionalidades

- Listagem de pacientes com nome, CPF e unidade de saúde.
- Busca parcial de pacientes pelo nome.
- Cadastro, consulta, edição e exclusão de pacientes.
- Carregamento das unidades de saúde no formulário.
- Validação de campos obrigatórios, CPF com 11 dígitos, CPF único, data de nascimento não futura e existência da unidade.
- Mensagens de sucesso e erro na interface, incluindo erros de validação retornados pela API.
- Três consultas SQL independentes do Eloquent, em `sql/consultas.sql`.

## Pré-requisitos

Antes de iniciar, instale:

- PHP 8.3 ou superior, com as extensões exigidas pelo Laravel e pelo driver MySQL (`pdo_mysql`).
- Composer.
- MySQL ou MariaDB.
- Git.

## Instalação e configuração

### 1. Clonar o repositório

```bash
git clone <URL_DO_REPOSITORIO>
cd <PASTA_DO_PROJETO>
```

Substitua os valores pelos dados reais do repositório.

### 2. Instalar dependências

```bash
composer install
```

### 3. Configurar o ambiente

Crie o arquivo `.env` a partir do exemplo:

```bash
cp .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

### 4. Criar e configurar o banco de dados

Crie um banco de dados MySQL ou MariaDB. Em seguida, configure as variáveis abaixo no arquivo `.env`, usando os dados do seu ambiente:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

Não compartilhe nem versione o arquivo `.env`.

### 5. Criar tabelas e dados iniciais

Execute:

```bash
php artisan migrate --seed
```

As migrations criam as tabelas, incluindo `health_units` e `patients`. O seeder cadastra três unidades de saúde para uso no formulário.

### 6. Iniciar a aplicação

```bash
php artisan serve
```

Acesse a aplicação em:

http://127.0.0.1:8000

O frontend é servido pelo Laravel. Não é necessário iniciar um servidor separado para os arquivos HTML, CSS e JavaScript.


## Endpoints da API

A API utiliza JSON e está disponível sob o prefixo `/api`.

| Método | Endpoint | Descrição |
|---|---|---|
| GET | `/api/health-units` | Lista as unidades de saúde |
| GET | `/api/patients` | Lista os pacientes |
| GET | `/api/patients?search=Maria` | Pesquisa pacientes por parte do nome |
| POST | `/api/patients` | Cadastra um paciente |
| GET | `/api/patients/{patient}` | Consulta um paciente |
| PUT | `/api/patients/{patient}` | Atualiza um paciente |
| DELETE | `/api/patients/{patient}` | Exclui um paciente |

As falhas de validação da API retornam HTTP `422`. O cadastro bem-sucedido retorna `201`, e a consulta de um paciente inexistente retorna `404`.

### Exemplo de corpo para cadastro

```json
{
  "name": "Maria da Silva",
  "cpf": "12345678901",
  "birth_date": "2000-01-01",
  "phone": null,
  "health_unit_id": 1
}
```

O CPF deve conter 11 dígitos numéricos. A validação dos dígitos verificadores do CPF não faz parte do escopo do desafio.

## Consultas SQL

O arquivo `sql/consultas.sql` contém as três consultas exigidas:

1. Pacientes com o nome da unidade de saúde, usando `JOIN`.
2. Total de pacientes por unidade, incluindo unidades sem pacientes, usando `LEFT JOIN` e `GROUP BY`.
3. Pesquisa de pacientes cujo nome contenha `Maria`, usando `LIKE`.

As consultas são SQL puro, sem Eloquent.

## Testes automatizados

Execute a suíte de testes com:

```bash
php artisan test
```

O arquivo `phpunit.xml` configura o ambiente de testes para usar SQLite em memória. Assim, os testes não precisam usar o banco MySQL de desenvolvimento.

Os testes de funcionalidade cobrem listagem, busca parcial, cadastro, CPF duplicado, formato do CPF, data futura, unidade inexistente, registro inexistente, edição e exclusão.

## Estrutura relevante do projeto

```text
app/
  Http/Controllers/Api/
  Models/
database/
  migrations/
  seeders/
public/
  css/app.css
  js/app.js
resources/
  views/patients.blade.php
routes/
  api.php
  web.php
sql/
  consultas.sql
tests/
  Feature/PatientApiTest.php
```

## Status

- [x] Migrations e seeder de unidades de saúde.
- [x] Relacionamentos Eloquent.
- [x] CRUD de pacientes por API.
- [x] Validações no backend.
- [x] Interface integrada à API.
- [x] Busca parcial e confirmação de exclusão.
- [x] Consultas SQL obrigatórias.
- [x] Testes automatizados de funcionalidades e validações.

## Versionamento

O histórico do Git registra a implementação em etapas. O arquivo `.env`, credenciais e dependências instaladas localmente não devem ser enviados ao repositório.