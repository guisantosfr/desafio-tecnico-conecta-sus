<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $patients = Patient::query()
            ->with('healthUnit:id,name')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(
                    'name',
                    'like',
                    '%' . $request->input('search') . '%'
                );
            })
            ->orderBy('name')
            ->get();

        return response()->json($patients);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'regex:/^[0-9]{11}$/', 'unique:patients,cpf'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'phone' => ['nullable', 'string', 'max:20'],
            'health_unit_id' => ['required', 'integer', 'exists:health_units,id']
        ]);

        $patient = Patient::create($data);
        $patient->load('healthUnit:id,name');

        return response()->json($patient, 201);
    }

    public function show(Patient $patient): JsonResponse
    {
        $patient->load('healthUnit:id,name');
        return response()->json($patient);
    }

    public function update(Request $request, Patient $patient): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'regex:/^[0-9]{11}$/', Rule::unique('patients', 'cpf')->ignore($patient->id)],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'phone' => ['nullable', 'string', 'max:20'],
            'health_unit_id' => ['required', 'integer', 'exists:health_units,id']
        ]);

        $patient->update($data);
        $patient->load('healthUnit:id,name');

        return response()->json($patient);
    }

    public function destroy(Patient $patient): JsonResponse
    {
        $patient->delete();
    
        return response()->json([
            'message' => 'Paciente excluído com sucesso',
        ]);
    }

}
