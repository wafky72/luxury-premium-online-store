<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsGlobalSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CmsController extends Controller
{
    /**
     * GET /api/v1/pages/{slug}
     * Returns page sections as React-renderable JSON.
     * Cached in Redis for 10 minutes, invalidated on publish.
     */
    public function show(string $slug): JsonResponse
    {
        $data = Cache::remember("cms:page:{$slug}", 600, function () use ($slug) {
            $page = CmsPage::where('slug', $slug)
                ->where('status', 'published')
                ->with(['sections'])
                ->firstOrFail();

            return [
                'slug'             => $page->slug,
                'title'            => $page->title,
                'meta_title'       => $page->meta_title,
                'meta_description' => $page->meta_description,
                'og_image'         => $page->og_image,
                'sections'         => $page->sections->map->toApiArray()->values(),
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * GET /api/v1/global-sections
     * Returns reusable global blocks (trust strip, announcement, etc.)
     */
    public function globalSections(): JsonResponse
    {
        $sections = Cache::remember('cms:global_sections', 3600, function () {
            return \App\Models\CmsGlobalSection::where('is_active', true)
                ->get()
                ->keyBy('key')
                ->map(fn($s) => ['type' => $s->type, 'props' => $s->props_json]);
        });

        return response()->json(['success' => true, 'data' => $sections]);
    }
}
