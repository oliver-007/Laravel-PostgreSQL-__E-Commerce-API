<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Routing\Controller;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories =  Category::latest('')->paginate(5);
        return CategoryResource::collection($categories)->additional([
            'success'=>true,
            'message'=> 'Category fetched successfully',
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
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
            return (new CategoryResource($category))->additional([
                'success'=>true,
                'message'=> 'Category created successfully'
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return (new CategoryResource($category))->additional([
            'success'=>true,
            'message'=>"{$category->name} category fetched successfully",
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
     $category->update($request->validated());

        return (new CategoryResource($category))->additional([
            'success'=>true,
            'message'=> 'Category updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryResource $category)
    {

    $deletedCategory = $category;

        $category->delete();

        return (new CategoryResource($deletedCategory))->additional([
            'success'=>true,
            'message'=> " {$deletedCategory->name} is deleted successfully ",
        ]);

    }
}
