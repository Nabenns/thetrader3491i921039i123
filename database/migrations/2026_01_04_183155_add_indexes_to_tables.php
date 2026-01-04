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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['status', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['status', 'ends_at']);
        });
    }
};
