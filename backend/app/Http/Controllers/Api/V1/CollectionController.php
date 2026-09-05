<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $collections
        ]);
    }

    public function show($slug)
    {
        $collection = Collection::where('slug', $slug)
            ->where('is_active', true)
            ->with(['products' => function($q) {
                $q->where('is_active', true)->with('primaryImage', 'activeVariants');
            }])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $collection
        ]);
    }
}
