<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use App\Models\Attendance;
use BackedEnum;
use Carbon\Carbon;
use Filament\Resources\Pages\Page;

class AttendanceHeatmap extends Page
{
    protected static string $resource = AttendanceResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected string $view = 'filament.pages.attendance-heatmap';

    public int $year;

    public array $attendanceCounts = [];

    public array $weeks = [];

    public int $maxCount = 1;

    public int $daysWithAttendance = 0;

    public int $totalEvents = 0;

    public float $averagePerActiveDay = 0.0;

    public array $monthLabels = [];

    public function mount(): void
    {
        $this->year = (int) request()->query('year', now()->year);

        $this->attendanceCounts = Attendance::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->whereYear('created_at', $this->year)
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        $this->daysWithAttendance = count(array_filter($this->attendanceCounts, fn ($count) => $count > 0));
        $this->totalEvents = array_sum($this->attendanceCounts);
        $this->averagePerActiveDay = $this->daysWithAttendance > 0 ? round($this->totalEvents / $this->daysWithAttendance, 2) : 0.0;
        $this->maxCount = count($this->attendanceCounts) > 0 ? max($this->attendanceCounts) : 1;
        $this->weeks = $this->buildYearWeeks($this->year);
        $this->monthLabels = $this->buildMonthLabels();
    }

    protected function buildYearWeeks(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfWeek(Carbon::MONDAY);
        $end = Carbon::create($year, 12, 31)->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        for ($date = $start->copy(); $date->lte($end); $date->addWeek()) {
            $week = [];

            for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
                $current = $date->copy()->addDays($dayIndex);
                $dayKey = $current->toDateString();

                $week[] = [
                    'date' => $dayKey,
                    'day' => $current->day,
                    'label' => $current->format('D'),
                    'in_year' => $current->year === $year,
                    'count' => $this->attendanceCounts[$dayKey] ?? 0,
                ];
            }

            $weeks[] = $week;
        }

        return $weeks;
    }

    protected function buildMonthLabels(): array
    {
        $labels = [];
        $previousMonth = null;

        foreach ($this->weeks as $weekIndex => $week) {
            foreach ($week as $day) {
                $current = Carbon::parse($day['date']);

                if (! $day['in_year']) {
                    continue;
                }

                if ($current->day === 1 && $current->format('M') !== $previousMonth) {
                    $labels[$weekIndex] = $current->format('M');
                    $previousMonth = $current->format('M');
                    break;
                }
            }
        }

        return $labels;
    }

    public function getYearOptionsProperty(): array
    {
        $currentYear = now()->year;
        $years = [];

        for ($year = $currentYear - 2; $year <= $currentYear + 1; $year++) {
            $years[$year] = (string) $year;
        }

        return $years;
    }

    public function getColorClass(int $count): string
    {
        if ($count === 0) {
            return 'bg-slate-100 border-slate-200';
        }

        $ratio = $count / max(1, $this->maxCount);

        return match (true) {
            $ratio <= 0.25 => 'bg-emerald-200',
            $ratio <= 0.5 => 'bg-emerald-300',
            $ratio <= 0.75 => 'bg-emerald-500 text-white',
            default => 'bg-emerald-700 text-white',
        };
    }
}
