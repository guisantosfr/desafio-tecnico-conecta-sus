<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HealthUnit;
use App\Models\Patient;

class PatientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_a_duplicate_cpf(): void
    {
        $healthUnit = HealthUnit::create([
            'name' => 'UBS Central',
            'city' => 'São Paulo',
        ]);

        Patient::create([
            'name' => 'Maria da Silva',
            'cpf' => '12345678901',
            'birth_date' => '2000-01-01',
            'phone' => null,
            'health_unit_id' => $healthUnit->id,
        ]);

        $response = $this->postJson('/api/patients', [
            'name' => 'João dos Santos',
            'cpf' => '12345678901',
            'birth_date' => '1990-10-10',
            'phone' => null,
            'health_unit_id' => $healthUnit->id,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cpf']);

        $this->assertDatabaseCount('patients', 1);
    }
}
