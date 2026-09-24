<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/categories
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Search by name if search parameter is provided
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Order by latest
        $categories = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully.',
            'data' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/categories
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $slug = Str::slug($validated['name']);
            $count = Category::where('slug', 'LIKE', "{$slug}%")->count();
            $validated['slug'] = $count ? "{$slug}-{$count}" : $slug;
        }

        // Default status to 1 (active) if not set
        if (!isset($validated['status'])) {
            $validated['status'] = 1;
        }

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     * GET /api/categories/{id}
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully.',
            'data' => $category
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /api/categories/{id}
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if (isset($validated['name']) && empty($validated['slug']) && !isset($request->slug)) {
            $slug = Str::slug($validated['name']);
            $count = Category::where('slug', 'LIKE', "{$slug}%")->where('id', '!=', $id)->count();
            $validated['slug'] = $count ? "{$slug}-{$count}" : $slug;
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/categories/{id}
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.'
        ], 200);
    }
}
