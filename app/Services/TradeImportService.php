<?php

namespace App\Services;

use App\Models\TradingJournal;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TradeImportService
{
    public function generateTemplate()
    {
        $headers = [
            'Open Date (YYYY-MM-DD HH:MM)',
            'Close Date (YYYY-MM-DD HH:MM)',
            'Pair (e.g. XAUUSD)',
            'Type (Buy/Sell)',
            'Lot Size',
            'Entry Price',
            'Exit Price',
            'Commission',
            'Swap',
            'Profit/Loss',
            'Notes',
            'Strategy',
            'Tags (comma separated)',
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            // Example Row
            fputcsv($file, [
                '2024-01-01 10:00',
                '2024-01-01 14:00',
                'XAUUSD',
                'Buy',
                '0.10',
                '2000.00',
                '2010.00',
                '-5.00',
                '-2.50',
                '100.00',
                'Example trade',
                'Breakout',
                'FOMO,News',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="trading_journal_template.csv"',
        ]);
    }

    public function import($file, $userId, $accountId)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Skip header
        
        $count = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            // Basic mapping based on template index
            // 0: Open Date, 1: Close Date, 2: Pair, 3: Type, 4: Lot, 5: Entry, 6: Exit, 
            // 7: Comm, 8: Swap, 9: PnL, 10: Notes, 11: Strategy, 12: Tags

            if (count($row) < 10) continue; // Skip invalid rows

            try {
                $data = [
                    'user_id' => $userId,
                    'account_id' => $accountId,
                    'open_date' => $this->parseDate($row[0]),
                    'close_date' => $this->parseDate($row[1]),
                    'pair' => strtoupper($row[2]),
                    'type' => strtolower($row[3]),
                    'lot_size' => (float) $row[4],
                    'entry_price' => (float) $row[5],
                    'exit_price' => (float) $row[6],
                    'commission' => (float) $row[7],
                    'swap' => (float) $row[8],
                    'pnl' => (float) $row[9],
                    'notes' => $row[10] ?? null,
                    'strategy' => $row[11] ?? null,
                    'tags' => isset($row[12]) ? array_map('trim', explode(',', $row[12])) : [],
                    'status' => ((float) $row[9]) > 0 ? 'win' : (((float) $row[9]) < 0 ? 'loss' : 'breakeven'),
                    'pips' => 0, // Todo: Calculate pips automatically
                ];

                TradingJournal::create($data);
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($count + 2) . ": " . $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'count' => $count,
            'errors' => $errors,
        ];
    }

    private function parseDate($dateString)
    {
        try {
            return \Carbon\Carbon::parse($dateString);
        } catch (\Exception $e) {
            return now();
        }
    }
}
