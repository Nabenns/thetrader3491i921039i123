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
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('duration_in_days')->nullable()->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dateTime('ends_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('duration_in_days')->nullable(false)->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dateTime('ends_at')->nullable(false)->change();
        });
    }
};
