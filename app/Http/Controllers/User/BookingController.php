<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $this->authorize('booking-manage');

        return response()->json(['success' => true]);
    }
}
