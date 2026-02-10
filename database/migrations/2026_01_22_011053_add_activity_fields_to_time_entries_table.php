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
        Schema::table('time_entries', function (Blueprint $table) {
            $table->enum('activity_type', ['development', 'maintenance', 'meeting', 'research', 'documentation', 'review', 'support', 'planning'])->default('development')->after('description');
            $table->json('attachments')->nullable()->after('activity_type');
            $table->boolean('manually_edited')->default(false)->after('attachments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropColumn(['activity_type', 'attachments', 'manually_edited']);
        });
    }
};
