<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\User;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // Retrieve the user associated with the transaction
        $user = $transaction->user;

        // dd($transaction->transaction_type->type->value);
        if($transaction->transaction_type->type->value == 'in')
        {
            // Update the ammount_balance in the User model
            $user->update([
                'ammount_balance' => $user->ammount_balance + $transaction->ammount
            ]);
        }else{
            $user->update([
                'ammount_balance' => $user->ammount_balance - $transaction->ammount
            ]);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
