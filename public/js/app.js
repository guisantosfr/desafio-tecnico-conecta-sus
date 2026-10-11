
const API_URL = '/api';

const form = document.querySelector('#patient-form');
const formTitle = document.querySelector('#form-title');
const saveButton = document.querySelector('#save-button');
const cancelButton = document.querySelector('#cancel-button');
const feedback = document.querySelector('#feedback');
const unitsSelect = document.querySelector('#health_unit_id');
const patientsTable = document.querySelector('#patients-table');
const searchForm = document.querySelector('#search-form');
const searchInput = document.querySelector('#search');
const clearSearchButton = document.querySelector('#clear-search');
const birthDateInput = document.querySelector('#birth_date');

let editingPatientId = null;

const today = new Date();
const year = today.getFullYear();
const month = String(today.getMonth() + 1).padStart(2, '0');
const day = String(today.getDate()).padStart(2, '0');

birthDateInput.max = `${year}-${month}-${day}`;

// Faz chamadas HTTP e converte respostas JSON.
async function apiRequest(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            Accept: 'application/json',
            ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            ...options.headers,
        },
    });

    const data = await response.json().catch(() => null);

    if (!response.ok) {
        const error = new Error('Não foi possível concluir a operação.');
        error.status = response.status;
        error.data = data;
        throw error;
    }

    return data;
}


function showFeedback(message, type = 'success', errors = []) {
    feedback.replaceChildren();
    feedback.className = `feedback ${type}`;
    feedback.hidden = false;

    const content = document.createElement('div');
    content.className = 'feedback-content';

    const text = document.createElement('p');
    text.textContent = message;
    content.appendChild(text);

    if (errors.length) {
        const list = document.createElement('ul');

        errors.forEach((error) => {
            const item = document.createElement('li');
            item.textContent = error;
            list.appendChild(item);
        });

        content.appendChild(list);
    }

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'feedback-close';
    closeButton.textContent = '×';
    closeButton.setAttribute('aria-label', 'Fechar mensagem');
    closeButton.addEventListener('click', () => {
        feedback.hidden = true;
    });

    feedback.append(content, closeButton);
}

function translateValidationMessage(message) {
    const translations = {
        'The cpf has already been taken.':
            'Este CPF já está cadastrado.',

        'The cpf field must be 11 characters.':
            'O CPF deve conter exatamente 11 dígitos.',

        'The cpf field format is invalid.':
            'O CPF deve conter apenas 11 dígitos numéricos.',

        'The name field is required.':
            'O nome é obrigatório.',

        'The cpf field is required.':
            'O CPF é obrigatório.',

        'The birth date field is required.':
            'A data de nascimento é obrigatória.',

        'The health unit id field is required.':
            'Selecione uma unidade de saúde.',

        'The selected health unit id is invalid.':
            'A unidade de saúde selecionada não existe.',

        'The birth date field must be a date before or equal to today.':
            'A data de nascimento não pode ser futura.',
    };

    return translations[message] ?? message;
}

function showApiError(error) {
    if (error.status === 422 && error.data?.errors) {
        const messages = Object.values(error.data.errors)
            .flat()
            .map(translateValidationMessage);

        showFeedback('Verifique os dados informados.', 'error', messages);
        return;
    }

    if (error.status === 404) {
        showFeedback('O registro solicitado não foi encontrado.', 'error');
        return;
    }

    showFeedback(
        error.data?.message || 'Ocorreu um erro. Tente novamente.',
        'error'
    );
}

function createCell(value) {
    const cell = document.createElement('td');
    cell.textContent = value ?? '';
    return cell;
}

async function loadHealthUnits() {
    const units = await apiRequest(`${API_URL}/health-units`);

    unitsSelect.replaceChildren(
        new Option('Selecione uma unidade', '')
    );

    units.forEach((unit) => {
        unitsSelect.add(new Option(unit.name, unit.id));
    });
}

async function loadPatients(search = '') {
    patientsTable.replaceChildren();

    const loadingRow = document.createElement('tr');
    const loadingCell = document.createElement('td');
    loadingCell.colSpan = 4;
    loadingCell.textContent = 'Carregando pacientes...';
    loadingRow.appendChild(loadingCell);
    patientsTable.appendChild(loadingRow);

    try {
        const params = new URLSearchParams();

        if (search.trim()) {
            params.set('search', search.trim());
        }

        const query = params.toString();
        const url = `${API_URL}/patients${query ? `?${query}` : ''}`;
        const patients = await apiRequest(url);

        patientsTable.replaceChildren();

        if (!patients.length) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 4;
            cell.textContent = 'Nenhum paciente encontrado.';
            row.appendChild(cell);
            patientsTable.appendChild(row);
            return;
        }

        patients.forEach((patient) => {
            const row = document.createElement('tr');

            row.appendChild(createCell(patient.name));
            row.appendChild(createCell(patient.cpf));
            row.appendChild(createCell(patient.health_unit?.name));

            const actionsCell = document.createElement('td');

            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.textContent = 'Editar';
            editButton.addEventListener('click', () => {
                startEditing(patient.id);
            });

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.textContent = 'Excluir';
            deleteButton.className = 'button-secondary';
            deleteButton.addEventListener('click', () => {
                deletePatient(patient.id, patient.name);
            });

            actionsCell.append(editButton, deleteButton);
            row.appendChild(actionsCell);
            patientsTable.appendChild(row);
        });
    } catch (error) {
        patientsTable.replaceChildren();
        showApiError(error);
    }
}

function resetForm() {
    form.reset();
    editingPatientId = null;

    formTitle.textContent = 'Cadastrar paciente';
    saveButton.textContent = 'Salvar paciente';
    cancelButton.hidden = true;

    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    birthDateInput.max = `${year}-${month}-${day}`;
}

async function startEditing(id) {
    try {
        const patient = await apiRequest(`${API_URL}/patients/${id}`);

        editingPatientId = patient.id;

        form.elements.name.value = patient.name;
        form.elements.cpf.value = patient.cpf;
        form.elements.birth_date.value = String(patient.birth_date).slice(0, 10);
        form.elements.phone.value = patient.phone ?? '';
        form.elements.health_unit_id.value = patient.health_unit_id;

        formTitle.textContent = 'Editar paciente';
        saveButton.textContent = 'Salvar alterações';
        cancelButton.hidden = false;

        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (error) {
        showApiError(error);
    }
}

form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const payload = {
        name: form.elements.name.value.trim(),
        cpf: form.elements.cpf.value.trim(),
        birth_date: form.elements.birth_date.value,
        phone: form.elements.phone.value.trim() || null,
        health_unit_id: Number(form.elements.health_unit_id.value),
    };

    saveButton.disabled = true;

    try {
        const isEditing = editingPatientId !== null;
        const url = isEditing
            ? `${API_URL}/patients/${editingPatientId}`
            : `${API_URL}/patients`;

        await apiRequest(url, {
            method: isEditing ? 'PUT' : 'POST',
            body: JSON.stringify(payload),
        });

        showFeedback(
            isEditing
                ? 'Paciente atualizado com sucesso.'
                : 'Paciente cadastrado com sucesso.'
        );

        resetForm();
        await loadPatients(searchInput.value);
    } catch (error) {
        showApiError(error);
    } finally {
        saveButton.disabled = false;
    }
});

async function deletePatient(id, name) {
    const confirmed = window.confirm(
        `Deseja realmente excluir o paciente "${name}"?`
    );

    if (!confirmed) {
        return;
    }

    try {
        await apiRequest(`${API_URL}/patients/${id}`, {
            method: 'DELETE',
        });

        if (editingPatientId === id) {
            resetForm();
        }

        showFeedback('Paciente excluído com sucesso.');
        await loadPatients(searchInput.value);
    } catch (error) {
        showApiError(error);
    }
}

searchForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    await loadPatients(searchInput.value);
});

clearSearchButton.addEventListener('click', async () => {
    searchInput.value = '';
    await loadPatients();
});

cancelButton.addEventListener('click', resetForm);

async function initialize() {
    try {
        await loadHealthUnits();
        await loadPatients();
    } catch (error) {
        showApiError(error);
    }
}

initialize();