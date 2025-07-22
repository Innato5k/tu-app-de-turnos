<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PatientService;
use App\Models\Patient; 
use Illuminate\Validation\Rule;

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
        $this->middleware('jwt.auth'); // Asegura que las rutas estén protegidas
    }

    public function index(Request $request)
    {
        // Obtiene el término de búsqueda de la URL (parámetro 'search')
        $searchQuery = $request->query('search');
        // Obtiene el número de elementos por página (parámetro 'per_page')
        $perPage = $request->query('per_page', 10); // Valor por defecto 10
        // Obtiene la página actual (parámetro 'page')
        $page = $request->query('page', 1); // Valor por defecto 1

        // Llama al servicio para obtener los pacientes filtrados y paginados
        // MODIFICADO: Pasando $perPage, $page y $searchQuery
        $patients = $this->patientService->getAllPatients($request , $searchQuery, $orderBy = 'name');

        // Ya no necesitas el if ($pacientes->isEmpty()) y lanzar una excepción aquí,
        // porque el paginador de Laravel ya devuelve una colección vacía si no hay resultados,
        // lo cual es una respuesta JSON válida para el frontend.

        return response()->json($patients);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(array $data) : ?Patient
    {
        return null;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cuil' => 'required|string|max:20|unique:patients,cuil',
            'email' => 'required|email|unique:patients,email',
            'phone' => 'nullable|string|max:20',
            'phone_opt' => 'nullable|string|max:20',
            'observations' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'medical_coverage' => 'nullable|string|max:255',
            'preferred_modality' => 'nullable|string|max:50',
        ]);

        return $this->patientService->registerPatient($request->all());
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
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'cuil' => [
                'sometimes',
                'string',
                'max:11',
                Rule::unique('patients')->ignore($id),
            ],
            'email' => [
                'sometimes',
                'email',
                Rule::unique('patients')->ignore($id),
            ],
            'phone' => 'nullable|string|max:20',
            'phone_opt' => 'nullable|string|max:20',
            'observations' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'medical_coverage' => 'nullable|string|max:255',
            'preferred_modality' => 'nullable|string|max:50',
            'is_active' => 'sometimes|boolean',
        ]);
        $patient = $this->patientService->updatePatient($id, $request->all());
        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }
        return response()->json([
            'message' => 'Patient updated successfully',
            'patient' => $patient
        ]);
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
