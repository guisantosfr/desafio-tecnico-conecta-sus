<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthUnit;
use Illuminate\Http\JsonResponse;

class HealthUnitController extends Controller
{
    public function index(): JsonResponse
    {
        $healthUnits = HealthUnit::query()
            ->orderBy('name')
            ->get(['id', 'name', 'city']);

        return response()->json($healthUnits);
    }
}
