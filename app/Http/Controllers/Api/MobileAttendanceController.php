<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MobileAttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Attendance::query()
            ->where('user_id', $user->id);

        return response()->json([
            'data' => $query->latest()->limit(50)->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'attendance_type' => ['required', Rule::in(['clock_in', 'clock_out'])],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $data['user_id'] = $user->id;

        // Check if user has an assigned showroom
        if (!$user->showroom_id) {
            return response()->json([
                'message' => 'You have not been assigned to a showroom. Contact your administrator.',
            ], 422);
        }

        $showroom = $user->showroom;
        $data['showroom_id'] = $showroom->id;

        $currentTime = Carbon::now();
        $workStart = Carbon::parse('09:00');
        $clockInDeadline = Carbon::parse('09:30');
        $workEnd = Carbon::parse('17:30');

        if ($data['attendance_type'] === 'clock_in' && $currentTime->greaterThan($clockInDeadline)) {
            return response()->json([
                'message' => 'Clock in is not allowed after 9:30 AM.',
            ], 422);
        }

        if ($data['attendance_type'] === 'clock_out' && ($currentTime->lessThan($workStart) || $currentTime->greaterThan($workEnd))) {
            return response()->json([
                'message' => 'Clock out must occur within working hours (9:00 AM - 5:30 PM).',
            ], 422);
        }

        // Validate geofence for clock_in
        if ($data['attendance_type'] === 'clock_in') {
            $distance = $this->haversineDistance(
                (float) $data['latitude'],
                (float) $data['longitude'],
                $showroom->latitude,
                $showroom->longitude
            );

            $radius = $showroom->geofence_radius_meters ?? 100;

            if ($distance > $radius) {
                return response()->json([
                    'message' => 'You are not within the authorized location area for clock-in. Distance: ' . round($distance, 2) . ' meters.',
                    'distance' => round($distance, 2),
                    'allowed_radius' => $radius,
                    'showroom_name' => $showroom->name,
                ], 422);
            }

            $data['is_within_geofence'] = true;
        } else {
            $data['is_within_geofence'] = true;
        }

        $attendance = Attendance::create($data);

        return response()->json([
            'data' => $attendance,
        ], 201);
    }

    protected function haversineDistance(float $latitudeFrom, float $longitudeFrom, float $latitudeTo, float $longitudeTo): float
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;

        return $earthRadius * 2 * asin(min(1, sqrt($a)));
    }
}
