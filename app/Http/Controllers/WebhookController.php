<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook:', $payload);

        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $signatureKey = $payload['signature_key'];
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? null;

        $serverKey = config('services.midtrans.server_key');
        $mySignatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $mySignatureKey) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $transaction = Transaction::where('midtrans_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction->status === 'paid') {
            return response()->json(['message' => 'Transaction already paid'], 200);
        }

        $newStatus = 'pending';

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $newStatus = 'challenge';
            } else {
                $newStatus = 'paid';
            }
        } else if ($transactionStatus == 'settlement') {
            $newStatus = 'paid';
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $newStatus = 'failed';
        } else if ($transactionStatus == 'pending') {
            $newStatus = 'pending';
        }

        $transaction->update([
            'status' => $newStatus,
            'payload' => $payload,
        ]);

        if ($newStatus === 'paid') {
            $this->activateSubscription($transaction);
        }

        return response()->json(['message' => 'OK']);
    }

    protected function activateSubscription(Transaction $transaction)
    {
        $package = $transaction->package;
        $user = $transaction->user;

        // Check if user already has active subscription
        $existingSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->first();

        $startsAt = now();
        
        if ($existingSubscription) {
            // Extend existing subscription
            $startsAt = $existingSubscription->ends_at ?? now();
            $existingSubscription->update(['status' => 'expired']); // Mark old as expired/replaced? Or just extend?
            // Usually we create a new one that starts after the old one, OR extend the old one.
            // PRD says "Status subscription divalidasi saat user mengakses...".
            // Let's create a new one.
        }

        $endsAt = $package->duration_in_days ? $startsAt->copy()->addDays($package->duration_in_days) : null;

        Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
        ]);
    }
}
