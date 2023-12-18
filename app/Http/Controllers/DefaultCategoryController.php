<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DefaultCategory;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreDefaultCategoryRequest;
use Illuminate\Support\Facades\Log;

class DefaultCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', DefaultCategory::class);
        return DefaultCategory::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDefaultCategoryRequest $request)
    {
        $this->authorize('create', DefaultCategory::class);
        $category = new DefaultCategory();
        $category->fill($request->validated());
        $category->save();
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'type' => 'sometimes|nullable|in:D,C',
            'name' => 'sometimes|nullable|string|max:30',
        ]);

        $category = DefaultCategory::findOrFail($id);

        $category->update([
            'type' => $request->input('type') ?? $category->type,
            'name' => $request->input('name') ?? $category->name,
        ]);
        

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Only admins can delete
        $this->authorize('delete', DefaultCategory::class);

        $defaultCategory = DefaultCategory::findOrFail($id);
        $defaultCategory->delete();

        return response()->json(['message' => 'Entity deleted successfully']);
    }
}
