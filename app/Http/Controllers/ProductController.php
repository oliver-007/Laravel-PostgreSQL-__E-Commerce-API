<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

    // $request->validate([
    //     'per_page'=>'nullable|integer|min:1|max:100'
    // ]);

    // $perPage = $request->input('per_page', 10);
    
    // OR
    
    $validated  = $request->validate([
        'per_page' => [
            'nullable',
            'integer',
            'min:1',
            'max:100'
        ]
    ]);

        $perPage = $validated['per_page'] ?? 10;

        $products = Product::with('category')->latest()->paginate($perPage) ;
        return  ProductResource::collection($products) ->additional([
            'success'=> true,
            'message'=> 'All Products with catgegory name fetched successfully'
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
            'success'=>true,
            'message'=> 'Product created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return (new ProductResource($product))->additional([
            'success'=> true,
            "message"=>" {$product->name} is loaded successfully  "
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

return (new ProductResource($product))->additional([
    'success'=>true,
    'message'=> " {$product->name} is updated successfully "
]);


       }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $destroyedProduct = $product;
         $product->delete();
    return response()->json([
        'success'=>true,
        'message'=> " {$destroyedProduct->name} is destroyed successfully "
    ]);
    }
}
