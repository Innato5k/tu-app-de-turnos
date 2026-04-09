<?php

namespace App\Http\Controllers;

use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\DTOs\Patient\PatientRequestDTO;
use App\DTOs\Patient\PatientUpdateRequestDTO;
use Illuminate\Http\Request;
use App\Services\PatientService;
use Symfony\Component\HttpFoundation\JsonResponse;

class PatientController extends Controller
{
    protected $patientService;
    /**
     * Constructor que inyecta el servicio de pacientes.
     *
     * @param PatientService $patientService
     */
    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
        $this->middleware('jwt.auth');
    }

    public function index(Request $request)
    {
        //TODO: pasar los parámetros de búsqueda, ordenamiento y paginación a la capa de servicio
        $searchQuery = $request->query('search');
        $perPage = $request->query('per_page', 10); 
        $page = $request->query('page', 1); 
        $patients = $this->patientService->getAllPatients($request , $searchQuery, $orderBy = 'name');

        return response()->json($patients);
    }

    public function listActivePatients()
    {
        //TODO: chequear si funciona o no.
        $patients = $this->patientService->getAllActivePatients();

        return response()->json($patients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        $dto = PatientRequestDTO::fromRequest($request->validated());
        $patient = $this->patientService->registerPatient($dto);
        return response()->json([
            'message' => 'Patient registered successfully',
            'patient' => $patient
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {        
        $id = (int)$id; 
        $patient = $this->patientService->findPatientById($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found   '.$id], 404);
        }

        return response()->json($patient);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, string $id)
    {
        $dto = PatientUpdateRequestDTO::fromRequest($request->validated());
        $patient = $this->patientService->updatePatient($id, $dto);
        return response()->json([
            'message' => 'Patient updated successfully',
            'patient' => $patient
        ], 200);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->patientService->deletePatient($id);

        if ($deleted) {
            return response()->json(['message' => 'Patient deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Patient not found'], 404);
        }
    }
}
