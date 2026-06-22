<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\UpcomingReleaseService;
use Twig\Environment;

class UpcomingReleaseController
{
    public function __construct(
        private UpcomingReleaseService $service,
        private Environment            $twig,
        private Request                $request,
    ) {}

    /**
     * GET /upcoming
     * Renderiza el calendario de próximos estrenos para el mes/año
     * solicitado (por defecto, el mes actual), junto con la lista
     * de estrenos de ese mes y, si hay un día seleccionado, los
     * estrenos de ese día puntual.
     */
    public function index(): void
    {
        $today = new \DateTimeImmutable();

        $year  = (int) $this->request->get('year', $today->format('Y'));
        $month = (int) $this->request->get('month', $today->format('n'));

        // Clamp básico para evitar meses inválidos vía query string.
        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }

        $releases = $this->service->getByMonth($year, $month);
        $counts   = $this->service->getCountsByMonth($year, $month);

        $selectedDate = $this->request->get('date');
        $selectedReleases = $selectedDate
            ? $this->service->getByDate($selectedDate)
            : [];

        $currentMonth = new \DateTimeImmutable("{$year}-{$month}-01");

        $this->twig->display('pages/upcoming-releases.html.twig', [
            'releases'         => $releases,
            'counts'           => $counts,
            'year'             => $year,
            'month'            => $month,
            'currentMonth'     => $currentMonth,
            'selectedDate'     => $selectedDate,
            'selectedReleases' => $selectedReleases,
            'prevMonth'        => $currentMonth->modify('-1 month'),
            'nextMonth'        => $currentMonth->modify('+1 month'),
        ]);
    }
}