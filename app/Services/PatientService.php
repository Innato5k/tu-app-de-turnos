<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientService
{
    public function registerPatient(array $data): Patient
    {
        if ($data === null || !is_array($data)) {
            throw new ValidationException('Invalid data provided for patient creation.');
        }
        // Validación de datos
        if (Patient::where('cuil', $data['cuil'])->exists() || Patient::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'cuil/mail' => ['Cuil or email already registered.'],
            ]);
        }

        if (empty($data['cuil']) || empty($data['email'])) {
            throw ValidationException::withMessages([
                'cuil/email' => ['Cuil and email are required.'],
            ]);
        }
        $data['birth_date'] = substr($data['birth_date'], 0, 10); // "YYYY-MM-DD"
            if (!$this->isValidDate($data['birth_date'], 'Y-m-d')) {
                throw ValidationException::withMessages([
                    'birth_date' => ['Invalid date format for birth_date. Expected format: YYYY-MM-DD'],
                ]);
            }

        return Patient::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'cuil' => $data['cuil'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'phone_opt' => $data['phone_opt'] ?? null,
            'observations' => $data['observations'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'province' => $data['province'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'medical_coverage' => $data['medical_coverage'] ?? null,
            'preferred_modality' => $data['preferred_modality'] ?? null,
        ]);
    }           

    
    /**
     * Obtiene todos los pacientes.
     *
     * @return \Illuminate\Database\Eloquent\Collection<Patient>
     */
    public function getAllPatients(Request $request, ?string $searchQuery = null , ?string $orderBy = null): LengthAwarePaginator 
    {
        $query = Patient::query();
        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                  ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
                  ->orWhere('email', 'like', '%' . $searchQuery . '%');
            });
        }
        
        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        $perPage = $request->input('per_page', 10); // Número de pacientes por página
        $page = $request->input('page', 1); // Página actual        
        // $pacientes = Patient::paginate($perPage, ['*'], 'page', $page)->sortBy('name');
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Encuentra un paciente por su ID.
     *
     * @param int $id
     * @return \App\Models\Patient|null
     */
    public function findPatientById(int $id): ?Patient
    {
        return Patient::find($id);
    }

    /**
     * Actualiza un paciente existente.
     *
     * @param int $id
     * @param array $data Los datos a actualizar. Puede incluir 'name', 'email', 'password'.
     * @return \App\Models\Patient|null El paciente actualizado, o null si no se encontró.
     */
    public function updatePatient(int $id, array $data): ?Patient
    {
        $Patient = $this->findPatientById($id);

        if (!$Patient) {
            return null;
        }
        // Validación de datos
        if (Patient::where('cuil', $data['cuil'])->where('id', '!=', $id)->exists() || Patient::where('email', $data['email'])->where('id', '!=', $id)->exists()) {
            throw ValidationException::withMessages([
            'license' => ['Cuil or email already registered.'],]); 
        }
        // Actualización de los campos del paciente
        if (isset($data['name'])) {
            $Patient->name = $data['name'];
        }
        if (isset($data['last_name'])) {
            $Patient->last_name = $data['last_name'];
        }
        if (isset($data['cuil']) && filter_var($data['cuil'], FILTER_VALIDATE_INT) !== false) {
            $Patient->cuil = (int)$data['cuil'];
        }
        if (isset($data['email'])) {
            $Patient->email = $data['email'];
        }
        if (isset($data['birth_date'])) {
            // Si viene en formato datetime, extrae solo la fecha
            $date = substr($data['birth_date'], 0, 10); // "YYYY-MM-DD"
            if ($this->isValidDate($date, 'Y-m-d')) {
                $Patient->birth_date = $date;
            }
        }
        if (isset($data['phone'])) {
            $Patient->phone = $data['phone'];
        }
        if (isset($data['phone_opt'])) {
            $Patient->phone_opt = $data['phone_opt'];
        }
        if (isset($data['observations'])) {
            $Patient->observations = $data['observations'];
        }
        if (isset($data['gender'])) {
            $Patient->gender = $data['gender'];
        }
        if (isset($data['address'])) {
            $Patient->address = $data['address'];
        }
        if (isset($data['city'])) {
            $Patient->city = $data['city'];
        }
        if (isset($data['province'])) {
            $Patient->province = $data['province'];
        }
        if (isset($data['postal_code'])) {
            $Patient->postal_code = $data['postal_code'];
        }
        if (isset($data['medical_coverage'])) {
            $Patient->medical_coverage = $data['medical_coverage'];
        }
        if (isset($data['preferred_modality'])) {
            $Patient->preferred_modality = $data['preferred_modality'];
        }
        if (isset($data['is_active'])) {
            $Patient->is_active = $data['is_active'];
        }


        $Patient->save();

        return $Patient;
    }

    /**
     * Elimina un paciente.
     *
     * @param int $id
     * @return bool True si se eliminó, false si no se encontró.
     */
    public function deletePatient(int $id): bool
    {
        //TODO: Implementar lógica para eliminar un paciente soft delete.


        $Patient = $this->findPatientById($id);

        if (!$Patient) {
            return false;
        }
        $Patient->delete(); // Utiliza soft delete para marcar el paciente como eliminado


        return $Patient->is_deleted = true; // Marca el paciente como eliminado
    }

    public function changeState(int $id): bool
    {
        //TODO: Implementar lógica para eliminar un paciente soft delete.


        $Patient = $this->findPatientById($id);

        if (!$Patient) {
             return false;
        }
         $Patient->is_active = !$Patient->is_active;
        

        return true; 
    }
   
    private function isValidDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
