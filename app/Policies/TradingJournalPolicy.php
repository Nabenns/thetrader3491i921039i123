<?php

namespace App\Policies;

use App\Models\TradingJournal;
use App\Models\User;

class TradingJournalPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TradingJournal $tradingJournal): bool
    {
        return $user->id === $tradingJournal->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TradingJournal $tradingJournal): bool
    {
        return $user->id === $tradingJournal->user_id;
    }
}
