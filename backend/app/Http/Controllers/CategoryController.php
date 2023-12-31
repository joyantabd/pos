<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryEditResource;
use App\Http\Resources\CategoryResource;
use App\Manager\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\LaravelIgnition\Http\Requests\UpdateConfigRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = (new Category())->getAllCategories($request->all());
        return CategoryResource::collection($categories);
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
    public function store(StoreCategoryRequest $request)
    {
        $category = $request->except('photo');
        $category['slug'] = Str::slug($request->input('slug'));
        $category['user_id'] = Auth::user()->id;
        if($request->has('photo'))
        {
            $file = $request->input('photo');
            $category['photo'] = $this->processImageUpload($file,$category['slug']);
        }
        (new Category())->storeCategory($category);
        return response()->json(['msg'=>'Insered Successfully','cls'=>'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
       return new CategoryEditResource($category);
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
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category_data = $request->except('photo');
        $category['slug'] = Str::slug($request->input('slug'));
    
        if($request->has('photo'))
        {
            $file = $request->input('photo');
            $category_data['photo'] = $this->processImageUpload($file,$category['slug'],$category->photo);
        }
        $category->update($category_data);
        return response()->json(['msg'=>'Updated Successfully','cls'=>'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {

        if(!empty($category))
        {
            ImageManager::deletePhoto(Category::IMAGE_UPLOAD_PATH,$category->photo);
            ImageManager::deletePhoto(Category::THUMB_IMAGE_UPLOAD_PATH,$category->photo);
        }
        $category->delete();
        return response()->json(['msg'=>'Deteleted Successfully','cls'=>'warning']);
    }

    public function getCategory(){
        $categories = (new Category())->getCategoryIdName();
        return response()->json($categories);
    }

    private function processImageUpload($file, $name, $existing_photo = null)
    {
        $width = 800;
        $height = 800;
        $width_thumb = 150;
        $height_thumb = 150;
        $path = Category::IMAGE_UPLOAD_PATH;
        $path_thumb = Category::THUMB_IMAGE_UPLOAD_PATH;

        if(!empty($existing_photo)){
        ImageManager::deletePhoto(Category::IMAGE_UPLOAD_PATH,$existing_photo);
        ImageManager::deletePhoto(Category::THUMB_IMAGE_UPLOAD_PATH,$existing_photo); 
         }

    $photo_name = ImageManager::uploadImage($name,$width,$height,$path,$file);
                  ImageManager::uploadImage($name,$width_thumb,$height_thumb,$path_thumb,$file);

    return $photo_name;
    }
}
