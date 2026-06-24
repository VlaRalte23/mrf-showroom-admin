<?php

return [
    'geofence_latitude' => env('ATTENDANCE_GEOFENCE_LATITUDE'),
    'geofence_longitude' => env('ATTENDANCE_GEOFENCE_LONGITUDE'),
    'geofence_radius_meters' => env('ATTENDANCE_GEOFENCE_RADIUS_METERS', 100),
];
