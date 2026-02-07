<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journal_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_journal_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('type')->nullable(); // entry, exit, analysis
            $table->timestamps();
        });

        // Migrate existing screenshots
        $journals = \Illuminate\Support\Facades\DB::table('trading_journals')->whereNotNull('screenshot')->get();
        foreach ($journals as $j) {
            \Illuminate\Support\Facades\DB::table('journal_images')->insert([
                'trading_journal_id' => $j->id,
                'image_path' => $j->screenshot,
                'type' => 'chart',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_images');
    }
};
