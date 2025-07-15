<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

        return Patient::create([
            'name' => $data['name'],
            'last_name' => $data['lastname'],
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
            'medical_coerage' => $data['medical_coerage'] ?? null,
        ]);
    }           

    
    /**
     * Obtiene todos los pacientes.
     *
     * @return \Illuminate\Database\Eloquent\Collection<Patient>
     */
    public function getAllPatients(): Collection
    {
        $pacientes = Patient::all();
        if ($pacientes->isEmpty()) {
            throw new \Exception('No patients found');
        }
        return $pacientes;
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
        if (isset($data['lastname'])) {
            $Patient->last_name = $data['lastname'];
        }
        if (isset($data['cuil'])) {
            $Patient->cuil = $data['cuil'];
        }
        if (isset($data['email'])) {
            $Patient->email = $data['email'];
        }
        if (isset($data['birth_date'])) {
            $Patient->birth_date = $data['birth_date'];
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
        if (isset($data['medical_coerage'])) {
            $Patient->medical_coerage = $data['medical_coerage'];
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

        return $Patient->delete();
    }
}
