<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('app_top_position_by_category', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->integer('position')->nullable()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_top_position_by_category', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->integer('position')->after('category_id');
        });
    }
};
