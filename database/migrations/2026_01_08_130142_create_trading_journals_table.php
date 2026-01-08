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
        Schema::create('trading_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pair');
            $table->enum('type', ['buy', 'sell']);
            $table->decimal('entry_price', 10, 5);
            $table->decimal('exit_price', 10, 5)->nullable();
            $table->decimal('lot_size', 8, 2);
            $table->decimal('pnl', 15, 2)->nullable();
            $table->decimal('pips', 10, 2)->nullable();
            $table->enum('status', ['open', 'closed', 'breakeven'])->default('open');
            $table->dateTime('open_date');
            $table->dateTime('close_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('screenshot')->nullable();
            $table->enum('emotion', ['neutral', 'fomo', 'revenge', 'confident', 'fearful', 'greedy'])->default('neutral');
            $table->string('strategy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_journals');
    }
};
