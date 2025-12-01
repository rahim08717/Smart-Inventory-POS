<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // ভবিষ্যতে API কল করার জন্য লাগবে

trait SmsTrait
{
    public function sendSms($phone, $message)
    {
        // ১. আপাতত আমরা লগ ফাইলে মেসেজটি সেভ করব (ফ্রি টেস্টিং)
        Log::info("SMS Sent to {$phone}: {$message}");

        // ২. ভবিষ্যতে যখন API কিনবেন, তখন নিচের কোডটি ব্যবহার করবেন:
        /*
        try {
            $response = Http::get('https://api.smsprovider.com/send', [
                'api_key' => 'YOUR_API_KEY',
                'number' => $phone,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error("SMS Failed: " . $e->getMessage());
        }
        */

        return true;
    }
}
