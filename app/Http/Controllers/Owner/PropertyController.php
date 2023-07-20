<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $this->authorize('properties-manage');

        return response()->json(['success' => 'true']);
    }
}
