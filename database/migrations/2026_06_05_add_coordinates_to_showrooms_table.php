<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('showrooms', function (Blueprint $table) {
            $table->double('latitude', 15, 8)->nullable()->after('location');
            $table->double('longitude', 15, 8)->nullable()->after('latitude');
            $table->double('geofence_radius_meters', 8, 2)->default(100)->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('showrooms', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'geofence_radius_meters']);
        });
    }
};
