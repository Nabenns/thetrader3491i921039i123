<?php

namespace App\Livewire;

use App\Models\Package;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

use Livewire\Attributes\Layout;

class Checkout extends Component
{
    #[Layout('layouts.app')]
    public Package $package;
    public $snapToken;
    
    public $couponCode = '';
    public $discount = 0;
    public $total = 0;
    public $couponMessage = '';
    public $couponType = ''; // 'fixed' or 'percent'

    public function mount(Package $package)
    {
        $this->package = $package;
        $this->total = $package->price;
    }

    public function applyCoupon()
    {
        $this->reset(['discount', 'total', 'couponMessage', 'couponType']);
        $this->total = $this->package->price;

        if (empty($this->couponCode)) {
            return;
        }

        $coupon = \App\Models\Coupon::where('code', $this->couponCode)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$coupon) {
            $this->addError('couponCode', 'Kode kupon tidak valid atau kadaluarsa.');
            return;
        }

        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            $this->addError('couponCode', 'Batas penggunaan kupon telah habis.');
            return;
        }

        // Always treat as percentage
        $this->discount = $this->package->price * ($coupon->value / 100);
        $this->couponType = 'percent';

        // Ensure discount doesn't exceed price
        $this->discount = min($this->discount, $this->package->price);
        $this->total = $this->package->price - $this->discount;
        $this->couponMessage = 'Kupon berhasil digunakan!';
    }

    public function pay(PaymentService $paymentService)
    {
        // Create Transaction
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'package_id' => $this->package->id,
            'midtrans_id' => 'TRX-' . time() . '-' . Str::random(5),
            'amount' => $this->total, // Use discounted total
            'status' => 'pending',
            'payment_type' => 'midtrans',
        ]);
        
        // Increment coupon usage if applied
        if ($this->discount > 0) {
             $coupon = \App\Models\Coupon::where('code', $this->couponCode)->first();
             if ($coupon) {
                 $coupon->increment('usage_count');
             }
        }

        // Get Snap Token
        // Prepare Midtrans Params
        $transactionDetails = [
            'order_id' => $transaction->midtrans_id,
            'gross_amount' => (int) $transaction->amount,
        ];

        $customerDetails = [
            'first_name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ];

        $itemDetails = [
            [
                'id' => $this->package->id,
                'price' => (int) $this->total, // Use discounted price
                'quantity' => 1,
                'name' => $this->package->name . ($this->discount > 0 ? ' (Discounted)' : ''),
            ]
        ];

        $this->snapToken = $paymentService->getSnapToken($transactionDetails, $customerDetails, $itemDetails);
        
        $this->dispatch('snap-token-received', token: $this->snapToken);
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}
