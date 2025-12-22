<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // 🔐 Admin – view all bookings
    public function index()
    {
        return Booking::latest()->get();
    }

    // 🌍 Public – store booking
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'service_type' => 'required|string',
            'booking_date' => 'required|date',
            'message' => 'nullable|string',
        ]);

        Booking::create($validated);

        return response()->json([
            'message' => 'Booking submitted successfully'
        ], 201);
    }
}
