<?php

namespace App\Services;

use App\Models\Patient;
use App\DTOs\Patient\PatientRequestDTO;
use App\DTOs\Patient\PatientUpdateRequestDTO;
use App\DTOs\Patient\PatientResponseDTO;
use App\DTOs\Patient\PatientFullResponseDTO;
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
            'is_active'          => $dto->isActive,
        ]);
    }

    /**
     * Obtiene todos los pacientes.
     *
     * @return \Illuminate\Database\Eloquent\Collection<Patient>
     */
    public function getAllPatients(Request $request, ?string $searchQuery = null, ?string $orderBy = null): LengthAwarePaginator
    {
        $query = Patient::query();

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        return $query->paginate($perPage, ['*'], 'page', $page)->through(fn($patient) => PatientResponseDTO::fromModel($patient));
    }

    public function getAllActivePatients(): Collection
    {
        return Patient::where('is_active', true)
            ->get()
            ->map(fn($patient) => PatientResponseDTO::fromModel($patient));
    }

    /**
     * Encuentra un paciente por su ID.
     *
     * @param int $id
     * @return \App\Models\Patient|null
     */
    public function findPatientById(int $id): ?PatientFullResponseDTO
    {
        $patient = Patient::find($id);

        return $patient ? PatientFullResponseDTO::fromModel($patient) : null;
    }

    /**
     * Actualiza un paciente existente.
     *
     * @param int $id
     * @param PatientRequestDTO $data Los datos a actualizar.
     * @return PatientFullResponseDTO|null El paciente actualizado, o null si no se encontró.
     */
    public function updatePatient(int $id, PatientUpdateRequestDTO $data): ?PatientFullResponseDTO
    {
        $patient = Patient::find($id);

        if (!$patient) {
            return null;
        }

        $patient->update([
            'name'               => $data->name,
            'last_name'          => $data->lastName,
            'cuil'               => $data->cuil,
            'email'              => $data->email,
            'phone'              => $data->phone,
            'phone_opt'          => $data->phoneOpt,
            'observations'       => $data->observations,
            'birth_date'         => $data->birthDate,
            'gender'             => $data->gender,
            'address'            => $data->address,
            'city'               => $data->city,
            'province'           => $data->province,
            'postal_code'        => $data->postalCode,
            'medical_coverage'   => $data->medicalCoverage,
            'preferred_modality' => $data->preferredModality,
            'is_active'          => $data->isActive,
        ]);

        return PatientFullResponseDTO::fromModel($patient->fresh());
    }

    /**
     * Elimina un paciente.
     *
     * @param int $id
     * @return bool True si se eliminó, false si no se encontró.
     */
    public function deletePatient(int $id): bool
    {

        /*Tres cosas que tenés que saber ahora que usás Soft Delete:
            Consultas Automáticas: A partir de ahora, Patient::all() o Patient::get() NO traerán a los pacientes borrados.
            Es como si no existieran, lo cual es genial para tu listado de "Pacientes Activos".

            Ver los Borrados: Si alguna vez necesitás un reporte de auditoría de quiénes fueron eliminados, usás:
            Patient::onlyTrashed()->get();

            Restaurar: Si el error fue humano y hay que volver atrás:
                $patient->restore(); (esto limpia el deleted_at y el paciente vuelve a la vida).*/
        $Patient = Patient::find($id);

        if (!$Patient) {
            return false;
        }
        $Patient->delete();

        return $Patient->is_deleted = true; // Marca el paciente como eliminado
    }
}
