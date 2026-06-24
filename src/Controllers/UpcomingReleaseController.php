<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\TitleListService;
use Twig\Environment;

class UpcomingReleaseController
{
    public function __construct(
        private TitleListService $titleListService,
        private Environment      $twig,
        private Request          $request,
    ) {}

    public function index(): void
    {
        $today = new \DateTimeImmutable();

        $year  = (int) $this->request->get('year',  $today->format('Y'));
        $month = (int) $this->request->get('month', $today->format('n'));

        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }

        // Traemos todos los upcoming agrupados por mes y filtramos el mes pedido
        $allByMonth = $this->titleListService->getUpcomingByMonth();
        $monthKey   = sprintf('%04d-%02d', $year, $month);
        $releases   = $allByMonth[$monthKey] ?? [];

        // Counts por día (para marcar el calendario)
        $counts = [];
        foreach ($releases as $row) {
            $counts[$row['release_date']] = ($counts[$row['release_date']] ?? 0) + 1;
        }

        $selectedDate     = $this->request->get('date');
        $selectedReleases = $selectedDate
            ? $this->titleListService->getUpcomingByDate($selectedDate)
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