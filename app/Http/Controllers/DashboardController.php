<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Announcement\AnnouncementResource;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Services\QuoteService;
use App\Services\AnnouncementService;
use App\Services\ProfessionalAppointmentsService;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
    protected QuoteService $quoteService,
    protected AnnouncementService $announcementService,
    protected ProfessionalAppointmentsService $professionalAppointmentsService,
    protected StatisticsService $statisticsService
) {}

    //Armar informacion para el dashboard, contiene la frase del día, anuncions de sistema, proxima cita e informacion de estadistica.
    public function index(): JsonResponse
    {
        $quote = $this->quoteService->getDailyQuote();
        $announcements = $this->announcementService->getAllAnnouncements();
        $nextAppointment = $this->professionalAppointmentsService->findNextAppointment();
        $stats = $this->statisticsService->getClosedMonthStats();
       
        $announcements = AnnouncementResource::collection($announcements);
        $nextAppointment = new AppointmentResource($nextAppointment);
        
        // Devolvemos todo en un solo viaje
        return response()->json([
            'quote' => [
                'text'   => $quote->text,
                'author' => $quote->author
            ],
            'announcements'  => $announcements,
            'next_appointment' => $nextAppointment,
            'stats' => $stats,
            'server_time'    => Carbon::now()->toDateTimeString()
        ], 200, [], JSON_UNESCAPED_UNICODE); // ¡Con tildes legibles!
    }
}