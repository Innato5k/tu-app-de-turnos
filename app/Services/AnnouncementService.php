<?php

namespace App\Services;

use App\Http\Resources\Announcement\AnnouncementResource;
use App\Models\Announcement;

class AnnouncementService
{
    
    /**
     * Obtiene todos los pacientes.
     *
     * @return \Illuminate\Database\Eloquent\Collection<Patient>
     */
    public function getAllAnnouncements()
    {
       return Announcement::with('author:id,name')
                            ->where('is_active', true)
                            ->latest()
                            ->get();
    }
}
