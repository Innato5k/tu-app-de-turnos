<?php

namespace App\Services;

use App\Models\Patient;
use App\DTOs\Patient\PatientRequestDTO;
use App\DTOs\Patient\PatientUpdateRequestDTO;
use App\Http\Resources\Patient\PatientResource;
use App\DTOs\Patient\PatientUpdatePreferredRequestDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PatientService
{
    public function registerPatient(PatientRequestDTO $dto): Patient
    {
        return Patient::create([
            'name'               => $dto->name,
            'last_name'          => $dto->lastName,
            'cuil'               => $dto->cuil,
            'email'              => $dto->email,
            'phone'              => $dto->phone,
            'phone_opt'          => $dto->phoneOpt,
            'observations'       => $dto->observations,
            'birth_date'         => $dto->birthDate,
            'gender'             => $dto->gender,
            'address'            => $dto->address,
            'city'               => $dto->city,
            'province'           => $dto->province,
            'postal_code'        => $dto->postalCode,
            'medical_coverage'   => $dto->medicalCoverage,
            'preferred_modality' => $dto->preferredModality,
            'preferred_cost'     => $dto->preferredCost,
            'is_active'          => $dto->isActive,
            'created_by_id'      => Auth::id(),
        ]);
    }

    /**
     * Obtiene todos los pacientes.
     *
     * @return \Illuminate\Database\Eloquent\Collection<Patient>
     */
    public function getAllPatients(Request $request, ?string $searchQuery = null, ?string $orderBy = null): LengthAwarePaginator
    {
        $query = Patient::withTrashed()->where('created_by_id', Auth::id())->orderBy($orderBy ?? 'last_name');

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getAllActivePatients(): Collection
    {
        return Patient::where('is_active', true)->where('created_by_id', Auth::id())
            ->get()
            ->map(fn($patient) => PatientResource::fromModel($patient));
    }

    /**
     * Encuentra un paciente por su ID.
     *
     * @param int $id
     * @return \App\Models\Patient|null
     */
    public function findPatientById(int $id): ?Patient
    {
        return Patient::withTrashed()->where('created_by_id', Auth::id())->findOrFail($id);
    }

    /**
     * Actualiza un paciente existente.
     *
     * @param int $id
     * @param PatientRequestDTO $data Los datos a actualizar.
     * @return Patient|null El paciente actualizado, o null si no se encontró.
     */
    public function updatePatient(int $id, PatientUpdateRequestDTO $data): ?Patient
    {
        
        $patient = $this->findPatientById($id);
        
        $patient->update($data->toArray());
        
        if (isset($data->is_active)) {
            if ($data->is_active) {
                $patient->restore();
            } else {
                $patient->delete();
            }
        }
        return $patient;
    }

    public function updatePatientPreferred(int $id, PatientUpdatePreferredRequestDTO $data): ?Patient
    {
        $patient = $this->findPatientById($id);
        $patient->update($data->toArray());
        return $patient;
    }

    /**
     * Elimina un paciente.
     *
     * @param int $id
     * @return bool True si se eliminó, false si no se encontró.
     */
    public function deletePatient(int $id): bool
    {
        if (auth()->id() === $id) {
            return false;
        }
        $patient = $this->findPatientById($id);
        return $patient->delete();
    }
}
