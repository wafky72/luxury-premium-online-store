<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * GET /api/v1/products
     * Supports: ?category=rings&collection=bridal&sort=price-asc&search=diamond&page=1
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::where('is_active', true)
            ->with(['primaryImage', 'activeVariants', 'category']);

        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->collection) {
            $query->whereHas('collection', fn($q) => $q->where('slug', $request->collection));
        }
        if ($request->search) {
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%")
                ->orWhere('sku', 'like', "%{$request->search}%")
            );
        }
        if ($request->featured) {
            $query->where('is_featured', true);
        }

        $query->when($request->sort, function ($q, $sort) {
            match($sort) {
                'price-asc'  => $q->orderBy('id'),   // real sort applied post-load (dynamic price)
                'price-desc' => $q->orderBy('id', 'desc'),
                'newest'     => $q->orderByDesc('created_at'),
                default      => $q->orderBy('sort_order'),
            };
        }, fn($q) => $q->orderBy('sort_order'));

        $products = $query->paginate($request->per_page ?? 24);

        return response()->json([
            'success' => true,
            'data'    => $products->items(),
            'meta'    => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/v1/products/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $product = Cache::remember("product:{$slug}", 1800, function () use ($slug) {
            return Product::where('slug', $slug)
                ->where('is_active', true)
                ->with(['productImages', 'activeVariants', 'category', 'collection'])
                ->firstOrFail();
        });

        return response()->json(['success' => true, 'data' => $product]);
    }

    /**
     * GET /api/v1/products/{slug}/related
     */
    public function related(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $related = Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(fn($q) => $q
                ->where('category_id', $product->category_id)
                ->orWhere('collection_id', $product->collection_id)
            )
            ->with(['primaryImage', 'activeVariants'])
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return response()->json(['success' => true, 'data' => $related]);
    }

    /**
     * GET /api/v1/products/{slug}/reviews
     */
    public function reviews(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $reviews = $product->reviews()
            ->latest()
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $reviews]);
    }

    /**
     * POST /api/v1/products/search
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $results = Product::where('is_active', true)
            ->where(fn($query) => $query
                ->where('name', 'like', "%{$q}%")
                ->orWhere('sku', 'like', "%{$q}%")
            )
            ->with(['primaryImage'])
            ->limit(8)
            ->get();

        return response()->json(['success' => true, 'data' => $results]);
    }
}
