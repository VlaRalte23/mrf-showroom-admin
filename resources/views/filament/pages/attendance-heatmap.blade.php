<x-filament::page>
    <style>
        .heatmap-page {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #0f172a;
            line-height: 1.5;
        }

        .heatmap-header,
        .heatmap-main {
            display: grid;
            gap: 1.5rem;
        }

        .heatmap-header {
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
        }

        .heatmap-title h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .heatmap-title p {
            margin: 0.5rem 0 0;
            color: #64748b;
            font-size: 0.95rem;
        }

        .heatmap-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: flex-end;
        }

        .heatmap-button,
        .heatmap-badge {
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #0f172a;
            padding: 0.8rem 1rem;
            text-decoration: none;
            font-size: 0.95rem;
            transition: background 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .heatmap-button:hover {
            background: #f8fafc;
        }

        .heatmap-badge {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .heatmap-grid-wrap {
            display: grid;
            grid-template-columns: minmax(0, 320px) minmax(0, 1fr);
            gap: 1.5rem;
        }

        .heatmap-card {
            border-radius: 1.5rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            padding: 1.5rem;
        }

        .heatmap-card h2 {
            margin: 0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: #334155;
            font-weight: 700;
        }

        .heatmap-summary {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
        }

        .heatmap-summary-row {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .summary-item {
            border-radius: 1rem;
            background: #f8fafc;
            padding: 1rem;
        }

        .summary-item p:first-child {
            margin: 0;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .summary-item p:last-child {
            margin: 0.6rem 0 0;
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
        }

        .heatmap-legend-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
        }

        .legend-item {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            border-radius: 1rem;
            background: #f8fafc;
            padding: 0.85rem;
            color: #334155;
        }

        .legend-dot {
            width: 1rem;
            height: 1rem;
            border-radius: 9999px;
            flex-shrink: 0;
        }

        .heatmap-chart {
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .heatmap-month-grid {
            display: grid;
            grid-template-columns: 40px repeat(auto-fit, minmax(20px, 1fr));
            gap: 0.4rem;
            align-items: center;
            margin-top: 0.5rem;
        }

        .heatmap-month-label {
            width: 20px;
            text-align: center;
            font-size: 0.72rem;
            color: #64748b;
        }

        .heatmap-body {
            display: flex;
            gap: 0.4rem;
            margin-top: 0.8rem;
        }

        .heatmap-weekdays {
            display: grid;
            grid-template-rows: repeat(7, 20px);
            gap: 0.4rem;
            color: #64748b;
            font-size: 0.72rem;
            min-width: 40px;
            align-items: center;
        }

        .heatmap-week {
            display: grid;
            grid-template-rows: repeat(7, 20px);
            gap: 0.4rem;
        }

        .heatmap-cell {
            width: 20px;
            height: 20px;
            border-radius: 0.3rem;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .heatmap-cell:hover {
            transform: scale(1.05);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .heatmap-cell.level-1 {
            background: #dcfce7;
            border-color: #dcfce7;
        }

        .heatmap-cell.level-2 {
            background: #86efac;
            border-color: #86efac;
        }

        .heatmap-cell.level-3 {
            background: #22c55e;
            border-color: #22c55e;
        }

        .heatmap-cell.level-4 {
            background: #166534;
            border-color: #166534;
        }

        .heatmap-info-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .heatmap-info {
            border-radius: 1rem;
            background: #f8fafc;
            padding: 1rem;
            color: #334155;
        }

        .heatmap-info h3 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .heatmap-info p {
            margin: 0.75rem 0 0;
            line-height: 1.6;
            font-size: 0.95rem;
            color: #475569;
        }

        .heatmap-intensity-grid {
            display: grid;
            grid-template-columns: auto repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
            align-items: center;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #64748b;
        }

        .heatmap-swatch {
            width: 14px;
            height: 14px;
            border-radius: 0.3rem;
            border: 1px solid #e2e8f0;
        }

        @media (max-width: 960px) {
            .heatmap-grid-wrap {
                grid-template-columns: 1fr;
            }

            .heatmap-summary-row,
            .heatmap-info-grid {
                grid-template-columns: 1fr;
            }

            .heatmap-body {
                width: 100%;
                overflow-x: auto;
            }
        }
    </style>

    <div class="heatmap-page">
        <div class="heatmap-header">
            <div class="heatmap-title">
                <p>See daily attendance activity over the selected year.</p>
            </div>

            <div class="heatmap-buttons">
                <a class="heatmap-button"
                    href="{{ \App\Filament\Resources\Attendances\AttendanceResource::getUrl('heatmap', ['year' => $year - 1]) }}">Previous
                    year</a>
                <a class="heatmap-button"
                    href="{{ \App\Filament\Resources\Attendances\AttendanceResource::getUrl('heatmap', ['year' => $year + 1]) }}">Next
                    year</a>
                <span class="heatmap-badge">Year: {{ $year }}</span>
            </div>
        </div>

        <div class="heatmap-grid-wrap">
            <div class="heatmap-card">
                <h2>Summary</h2>
                <div class="heatmap-summary">
                    <div class="heatmap-summary-row">
                        <div class="summary-item">
                            <p>Attendance days</p>
                            <p>{{ $daysWithAttendance }}</p>
                        </div>
                        <div class="summary-item">
                            <p>Total events</p>
                            <p>{{ $totalEvents }}</p>
                        </div>
                    </div>
                    <div class="heatmap-summary-row">
                        <div class="summary-item">
                            <p>Max daily events</p>
                            <p>{{ $maxCount }}</p>
                        </div>
                        <div class="summary-item">
                            <p>Average per active day</p>
                            <p>{{ $averagePerActiveDay }}</p>
                        </div>
                    </div>
                </div>

                <div class="heatmap-legend-grid">
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#cbd5e1;"></span>
                        <span>0 events</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#dcfce7;"></span>
                        <span>Low</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#86efac;"></span>
                        <span>Moderate</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#22c55e;"></span>
                        <span>Busy</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#166534;"></span>
                        <span>Very busy</span>
                    </div>
                </div>
            </div>

            <div class="heatmap-card">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                    <div>
                        <h2>Attendance heatmap</h2>
                        <p style="margin:0.5rem 0 0;color:#64748b;font-size:0.95rem;">Daily attendance activity shown as
                            a compact year grid.</p>
                    </div>
                    <span class="heatmap-badge" style="background:#f8fafc;">GitHub style</span>
                </div>

                <div class="heatmap-chart">
                    <div class="heatmap-month-grid">
                        <div></div>
                        @foreach ($weeks as $weekIndex => $week)
                            <div class="heatmap-month-label">{{ $monthLabels[$weekIndex] ?? '' }}</div>
                        @endforeach
                    </div>

                    <div class="heatmap-body">
                        <div class="heatmap-weekdays">
                            <span>Mon</span>
                            <span></span>
                            <span>Wed</span>
                            <span></span>
                            <span>Fri</span>
                            <span></span>
                            <span>Sun</span>
                        </div>
                        <div style="display:flex;gap:0.4rem;">
                            @foreach ($weeks as $week)
                                <div class="heatmap-week">
                                    @foreach ($week as $day)
                                        @php
                                            if (!$day['in_year']) {
                                                $levelClass = 'heatmap-cell';
                                            } elseif ($day['count'] === 0) {
                                                $levelClass = 'heatmap-cell';
                                            } elseif ($day['count'] / max(1, $maxCount) <= 0.25) {
                                                $levelClass = 'heatmap-cell level-1';
                                            } elseif ($day['count'] / max(1, $maxCount) <= 0.5) {
                                                $levelClass = 'heatmap-cell level-2';
                                            } elseif ($day['count'] / max(1, $maxCount) <= 0.75) {
                                                $levelClass = 'heatmap-cell level-3';
                                            } else {
                                                $levelClass = 'heatmap-cell level-4';
                                            }
                                        @endphp
                                        <div title="{{ \Illuminate\Support\Carbon::parse($day['date'])->format('D, M j, Y') }} - {{ $day['count'] }} event(s)"
                                            role="img"
                                            aria-label="{{ \Illuminate\Support\Carbon::parse($day['date'])->toDateString() }}: {{ $day['count'] }} events"
                                            class="{{ $levelClass }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="heatmap-info-grid">
                    <div class="heatmap-info">
                        <h3>How to read this chart</h3>
                        <p>Darker blocks mean more attendance events. Light gray blocks are days with no attendance, and
                            the year is divided into weekly columns like a contribution graph.</p>
                    </div>
                    <div class="heatmap-info">
                        <div class="heatmap-intensity-grid">
                            <span style="font-weight:700;color:#334155;">Intensity</span>
                            <span class="heatmap-swatch" style="background:#f1f5f9"></span>
                            <span class="heatmap-swatch" style="background:#dcfce7"></span>
                            <span class="heatmap-swatch" style="background:#86efac"></span>
                            <span class="heatmap-swatch" style="background:#22c55e"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
