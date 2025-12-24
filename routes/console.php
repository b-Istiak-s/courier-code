<?php

use App\Models\Booking;
use Enan\PathaoCourier\Facades\PathaoCourier;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {
    $bookings = Booking::where('courier_service', 'pathao')
        ->whereNotNull('pathao_consignment_ids')
        ->whereNotIn('courier_status', ['Delivered', 'Pickup Cancel'])
        ->get();

    try {
        foreach ($bookings as $booking) {
            try {
                // Get order details from Pathao
                $response = PathaoCourier::VIEW_ORDER($booking->pathao_consignment_ids);

                if (isset($response['data']['order_status'])) {
                    $pathaoStatus = $response['data']['order_status'];

                    // Update if status has changed
                    if ($booking->courier_status !== $pathaoStatus) {
                        $booking->update([
                            'courier_status' => $pathaoStatus
                        ]);

                        Log::info("Updated booking {$booking->order_id} status to: {$pathaoStatus}");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to update booking {$booking->order_id}: " . $e->getMessage());
            }
        }
    } catch (\Exception $e) {
        Log::error('Transaction failed, rolled back all updates: ' . $e->getMessage());
    }
})->everyThirtyMinutes();
