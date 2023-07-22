<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $this->authorize('properties-manage');

        return response()->json(['success' => 'true']);
    }

    public function store(StorePropertyRequest $request): Property
    {
        $this->authorize('properties-manage');

        return Property::create($request->validated());
    }
}
