<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->with(['children', 'products' => function($q) {
                $q->where('is_active', true)->with('primaryImage', 'activeVariants');
            }])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }
}
