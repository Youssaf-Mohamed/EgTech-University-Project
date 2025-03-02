<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $regions = [
            'Cairo',
            'Giza',
            'Alexandria',
            'Dakahlia',
            'Sharqia',
            'Monufia',
            'Qalyubia',
            'Beheira',
            'Gharbia',
            'Port Said',
            'Damietta',
            'Ismailia',
            'Suez',
            'Kafr el-Sheikh',
            'Fayoum',
            'Beni Suef',
            'Minya',
            'Asyut',
            'Sohag',
            'Qena',
            'Luxor',
            'Aswan',
            'New Valley',
            'Matruh',
            'Red Sea',
            'North Sinai',
            'South Sinai'
        ];

        foreach ($regions as $region) {
            DB::table('regions')->insert(['name' => $region]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
