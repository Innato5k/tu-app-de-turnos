<?php

namespace App\Http\Controllers;

use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\DTOs\Patient\PatientRequestDTO;
use App\DTOs\Patient\PatientUpdateRequestDTO;
use App\Http\Resources\Patient\PatientResource;
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

        return PatientResource::collection($patients);
    }

    public function listActivePatients(Request $request)
    {
        //TODO: chequear si funciona o no.
        //dd($request->query());
        $patients = $this->patientService->getAllPatients($request, $request->query('search'), $orderBy = 'name');


        return PatientResource::collection($patients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        $dto = PatientRequestDTO::fromRequest($request->validated());
        $patient = $this->patientService->registerPatient($dto);
        return new PatientResource($patient);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {  
        try
        {
            $id = (int)$id; 
            $patient = $this->patientService->findPatientById($id);
            return new PatientResource($patient);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Patient not found'], 404);
        } 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, string $id)
    {
        $dto = PatientUpdateRequestDTO::fromRequest($request->validated());
        $patient = $this->patientService->updatePatient($id, $dto);
        return new PatientResource($patient);
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
