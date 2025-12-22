<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact; // ✅ THIS LINE IS CRITICAL
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Store contact form (PUBLIC)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string',
            'email'   => 'required|email',
            'phone'   => 'required|string',
            'message' => 'required|string',
        ]);

        Contact::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Your message has been received'
        ]);
    }

    /**
     * Admin: fetch all contacts (PROTECTED)
     */
    public function index()
{
    return \App\Models\Contact::latest()->get();
}

}
