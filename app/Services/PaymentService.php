<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class PaymentService
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

    protected function configureMidtrans()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        if (app()->isLocal()) {
            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_HTTPHEADER => [],
            ];
        }
    }

    public function getSnapToken($transactionDetails, $customerDetails = null, $itemDetails = null)
    {
        $params = [
            'transaction_details' => $transactionDetails,
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
        ];

        return Snap::getSnapToken($params);
    }
}
