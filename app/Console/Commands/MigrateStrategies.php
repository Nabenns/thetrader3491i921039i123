<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TradingJournal;
use App\Models\TradingStrategy;
use App\Models\User;

class MigrateStrategies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strategy:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing string strategies to TradingStrategy models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting strategy migration...');

        // Get all journals with a strategy string but no strategy_id
        $journals = TradingJournal::whereNotNull('strategy')
            ->where('strategy', '!=', '')
            ->whereNull('strategy_id')
            ->get();

        $count = $journals->count();
        $this->info("Found {$count} journals to migrate.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($journals as $journal) {
            $strategyName = trim($journal->strategy);

            if (empty($strategyName)) {
                $bar->advance();
                continue;
            }

            // Find or create the strategy for the user
            $strategy = TradingStrategy::firstOrCreate(
                [
                    'user_id' => $journal->user_id,
                    'name' => $strategyName
                ],
                [
                    'description' => 'Migrated from legacy data.',
                    'color' => $this->randomColor(),
                ]
            );

            // Update the journal
            $journal->strategy_id = $strategy->id;
            $journal->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Strategy migration completed successfully!');
    }

    private function randomColor()
    {
        $colors = [
            '#ef4444', // Red
            '#f97316', // Orange
            '#f59e0b', // Amber
            '#eab308', // Yellow
            '#84cc16', // Lime
            '#22c55e', // Green
            '#10b981', // Emerald
            '#14b8a6', // Teal
            '#06b6d4', // Cyan
            '#0ea5e9', // Sky
            '#3b82f6', // Blue
            '#6366f1', // Indigo
            '#8b5cf6', // Violet
            '#a855f7', // Purple
            '#d946ef', // Fuchsia
            '#ec4899', // Pink
            '#f43f5e', // Rose
        ];

        return $colors[array_rand($colors)];
    }
}
