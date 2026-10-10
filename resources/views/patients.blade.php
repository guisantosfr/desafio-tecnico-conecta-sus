
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pacientes</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    <main class="container">
        <header class="page-header">
            <h1>Cadastro de Pacientes</h1>
            <p>Gerencie pacientes e suas unidades de saúde.</p>
        </header>

        <div id="feedback" class="feedback" role="status"
             aria-live="polite" hidden></div>

        <section class="panel">
            <h2 id="form-title">Cadastrar paciente</h2>

            <form id="patient-form">
                <div class="form-grid">
                    <div class="field">
                        <label for="name">Nome completo *</label>
                        <input id="name" name="name" required maxlength="255">
                    </div>

                    <div class="field">
                        <label for="cpf">CPF (11 dígitos) *</label>
                        <input id="cpf" name="cpf" required
                               inputmode="numeric" pattern="[0-9]{11}"
                               maxlength="11" placeholder="12345678901">
                    </div>

                    <div class="field">
                        <label for="birth_date">Data de nascimento *</label>
                        <input id="birth_date" name="birth_date"
                               type="date" required>
                    </div>

                    <div class="field">
                        <label for="phone">Telefone</label>
                        <input id="phone" name="phone" type="tel"
                               maxlength="20">
                    </div>

                    <div class="field">
                        <label for="health_unit_id">Unidade de saúde *</label>
                        <select id="health_unit_id" name="health_unit_id"
                                required>
                            <option value="">Selecione uma unidade</option>
                        </select>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" id="save-button">
                        Salvar paciente
                    </button>
                    <button type="button" id="cancel-button"
                            class="button-secondary" hidden>
                        Cancelar edição
                    </button>
                </div>
            </form>
        </section>

        <section class="panel">
            <h2>Pacientes cadastrados</h2>

            <form id="search-form" class="search-form">
                <div class="field search-field">
                    <label for="search">Pesquisar por nome</label>
                    <input id="search" name="search"
                           placeholder="Digite um nome">
                </div>
                <button type="submit">Pesquisar</button>
                <button type="button" id="clear-search"
                        class="button-secondary">Limpar</button>
            </form>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Unidade de saúde</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="patients-table">
                        <tr>
                            <td colspan="4">Carregando pacientes...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>