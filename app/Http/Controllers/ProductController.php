<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $validated = $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'search' => 'nullable|string|max:255',
            'sort_by' => 'nullable|in:price_acc, price_desc, latest',
        ]);

        $query = Product::with('category');

        // Filter by Category
        if (! empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        // Search by Name or Description
        if (! empty($validated['search'])) {
            $query->where(function ($q) use ($validated) {
                $q->where('name', 'like', '%'.$validated['search'].'%')
                    ->orWhere('description', 'like', '%'.$validated['search'].'%');
            });
        }

        // Sorting
        match ($validated['sort_by'] ?? 'latest') {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $perPage = $validated['per_page'] ?? 10;
        $products = $query->paginate($perPage);

        return ProductResource::collection($products)->additional([
            'success' => true,
            'message' => 'All Products with catgegory name fetched successfully',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        $product->load('category');

        return (new ProductResource($product))->additional([
            'success' => true,
            'message' => 'Product created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category');

        return (new ProductResource($product))->additional([
            'success' => true,
            'message' => " {$product->name} is fetched successfully  ",
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $validatedRequest = $request->validated();
        $product->update($validatedRequest);
        $product->load('category');

        return (new ProductResource($product))->additional([
            'success' => true,
            'message' => " {$product->name} is updated successfully ",
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => " {$productName} was deleted successfully ",
        ]);
    }
}
