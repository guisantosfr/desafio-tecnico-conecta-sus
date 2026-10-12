<?php

namespace Tests\Feature;

use App\Models\HealthUnit;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientApiTest extends TestCase
{
    use RefreshDatabase;

    private HealthUnit $healthUnit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->healthUnit = HealthUnit::create([
            'name' => 'UBS Central',
            'city' => 'São Paulo',
        ]);
    }

    public function test_it_lists_health_units(): void
    {
        $response = $this->getJson('/api/health-units');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $this->healthUnit->id,
                'name' => 'UBS Central',
                'city' => 'São Paulo',
            ]);
    }

    public function test_it_lists_patients_with_their_health_unit(): void
    {
        $patient = $this->createPatient();

        $this->getJson('/api/patients')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $patient->id,
                'name' => 'Maria da Silva',
                'cpf' => '12345678901',
                'health_unit' => [
                    'id' => $this->healthUnit->id,
                    'name' => 'UBS Central',
                ],
            ]);
    }

    public function test_it_searches_patients_by_partial_name(): void
    {
        $this->createPatient([
            'name' => 'Maria da Silva',
            'cpf' => '12345678901',
        ]);

        $this->createPatient([
            'name' => 'João dos Santos',
            'cpf' => '98765432100',
        ]);

        $this->getJson('/api/patients?search=Mari')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Maria da Silva']);
    }

    public function test_it_creates_a_patient(): void
    {
        $payload = [
            'name' => 'Ana Oliveira',
            'cpf' => '11122233344',
            'birth_date' => '1995-06-15',
            'phone' => '86999999999',
            'health_unit_id' => $this->healthUnit->id,
        ];

        $this->postJson('/api/patients', $payload)
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Ana Oliveira',
                'cpf' => '11122233344',
                'health_unit_id' => $this->healthUnit->id,
            ]);

        $this->assertDatabaseHas('patients', [
            'name' => 'Ana Oliveira',
            'cpf' => '11122233344',
        ]);
    }

    public function test_it_rejects_a_duplicate_cpf(): void
    {
        $this->createPatient([
            'name' => 'Maria da Silva',
            'cpf' => '12345678901',
        ]);

        $this->postJson('/api/patients', [
            'name' => 'João dos Santos',
            'cpf' => '12345678901',
            'birth_date' => '1990-10-10',
            'phone' => null,
            'health_unit_id' => $this->healthUnit->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cpf']);

        $this->assertDatabaseCount('patients', 1);
    }

    public function test_it_rejects_a_cpf_that_does_not_contain_eleven_digits(): void
    {
        $this->postJson('/api/patients', [
            'name' => 'Ana Oliveira',
            'cpf' => '12345',
            'birth_date' => '1995-06-15',
            'phone' => null,
            'health_unit_id' => $this->healthUnit->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cpf']);

        $this->assertDatabaseCount('patients', 0);
    }

    public function test_it_rejects_a_future_birth_date(): void
    {
        $this->postJson('/api/patients', [
            'name' => 'Ana Oliveira',
            'cpf' => '11122233344',
            'birth_date' => now()->addDay()->toDateString(),
            'phone' => null,
            'health_unit_id' => $this->healthUnit->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['birth_date']);

        $this->assertDatabaseCount('patients', 0);
    }

    public function test_it_rejects_a_nonexistent_health_unit(): void
    {
        $this->postJson('/api/patients', [
            'name' => 'Ana Oliveira',
            'cpf' => '11122233344',
            'birth_date' => '1995-06-15',
            'phone' => null,
            'health_unit_id' => 99999,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['health_unit_id']);

        $this->assertDatabaseCount('patients', 0);
    }

    public function test_it_returns_not_found_for_a_nonexistent_patient(): void
    {
        $this->getJson('/api/patients/99999')->assertNotFound();
    }

    public function test_it_updates_a_patient_without_rejecting_its_own_cpf(): void
    {
        $patient = $this->createPatient([
            'name' => 'Maria da Silva',
            'cpf' => '12345678901',
        ]);

        $this->putJson("/api/patients/{$patient->id}", [
            'name' => 'Maria Silva Atualizada',
            'cpf' => '12345678901',
            'birth_date' => '2000-01-01',
            'phone' => null,
            'health_unit_id' => $this->healthUnit->id,
        ])
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Maria Silva Atualizada',
                'cpf' => '12345678901',
            ]);

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'name' => 'Maria Silva Atualizada',
            'cpf' => '12345678901',
        ]);
    }

    public function test_it_rejects_updating_a_patient_to_another_patients_cpf(): void
    {
        $firstPatient = $this->createPatient([
            'name' => 'Maria da Silva',
            'cpf' => '12345678901',
        ]);

        $secondPatient = $this->createPatient([
            'name' => 'João dos Santos',
            'cpf' => '98765432100',
        ]);

        $this->putJson("/api/patients/{$secondPatient->id}", [
            'name' => 'João dos Santos',
            'cpf' => $firstPatient->cpf,
            'birth_date' => '1990-10-10',
            'phone' => null,
            'health_unit_id' => $this->healthUnit->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cpf']);

        $this->assertDatabaseHas('patients', [
            'id' => $secondPatient->id,
            'cpf' => '98765432100',
        ]);
    }

    public function test_it_deletes_a_patient(): void
    {
        $patient = $this->createPatient();

        $this->deleteJson("/api/patients/{$patient->id}")
            ->assertOk()
            ->assertJson([
                'message' => 'Paciente excluído com sucesso',
            ]);

        $this->assertDatabaseMissing('patients', [
            'id' => $patient->id,
        ]);
    }

    private function createPatient(array $overrides = []): Patient
    {
        return Patient::create(array_merge([
            'name' => 'Maria da Silva',
            'cpf' => '12345678901',
            'birth_date' => '2000-01-01',
            'phone' => null,
            'health_unit_id' => $this->healthUnit->id,
        ], $overrides));
    }
}