<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Bookings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function lists()
    {
        $bookings = Bookings::orderBy('id', 'desc')->paginate(5);
        return view('orders.index', compact('bookings'));
    }

    public function index()
    {
        return view('orders.order');
    }

    public function store(Request $request)
    {
        // 🔹 Validasi data input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'console_type' => 'required|string|in:PS4,PS5',
            'booking_date' => 'required|date',
            'total_price' => 'required|numeric|min:1',
        ]);

        // 🔹 Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // 🔹 Simpan Data Booking ke Database
        $booking = Bookings::create([
            'order_id' => 'ORDER-' . time(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'console_type' => $request->console_type,
            'booking_date' => $request->booking_date,
            'total_price' => $request->total_price,
            'payment_status' => 'pending',
        ]);

        // 🔹 Data untuk Midtrans
        $transaction = [
            'transaction_details' => [
                'order_id' => $booking->order_id,
                'gross_amount' => $request->total_price,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ],
            'item_details' => [
                [
                    'id' => $request->console_type,
                    'price' => $request->total_price,
                    'quantity' => 1,
                    'name' => 'Rental ' . $request->console_type
                ]
            ]
        ];

        // 🔹 Mendapatkan Token Pembayaran Midtrans
        try {
            $snapToken = Snap::getSnapToken($transaction);

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
