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
        Schema::table('trading_journals', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->constrained('trading_accounts')->nullOnDelete()->after('user_id');
            $table->decimal('commission', 10, 2)->default(0)->after('profit_loss');
            $table->decimal('swap', 10, 2)->default(0)->after('commission');
            $table->json('tags')->nullable()->after('notes');
            $table->string('magic_number')->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('trading_journals', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn(['account_id', 'commission', 'swap', 'tags', 'magic_number']);
        });
    }
};
