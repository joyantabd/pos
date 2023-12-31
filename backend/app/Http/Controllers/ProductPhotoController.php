<?php

namespace App\Http\Controllers;

use App\Models\ProductPhoto;
use App\Http\Requests\StoreProductPhotoRequest;
use App\Http\Requests\UpdateProductPhotoRequest;
use App\Manager\ImageManager;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProductPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreProductPhotoRequest $request,$id)
    {
        if($request->has('photos')){
            $product_slug = Product::whereId($id)->pluck('slug')->first();

            foreach($request->photos as $photo)
            {
                $photo_data['product_id'] = $id;
                $photo_data['is_primary'] = $photo['is_primary'];
                $name = Str::slug($product_slug.'-'.Carbon::now()->toDayDateTimeString().'-'.random_int(10000,99999));
                $photo_data['photo'] = ImageManager::processImageUpload($photo['photo'],$name,
                ProductPhoto::IMAGE_UPLOAD_PATH,ProductPhoto::PHOTO_WIDTH,ProductPhoto::PHOTO_HEIGHT,
                ProductPhoto::THUMB_IMAGE_UPLOAD_PATH,ProductPhoto::PHOTO_THUMB_WIDTH,ProductPhoto::PHOTO_THUMB_HEIGHT);

                ProductPhoto::create($photo_data);
            }

            return response()->json(['msg'=>'Photo Uploaded Successfully','cls'=>'success']);
        }

       
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductPhoto $productPhoto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductPhoto $productPhoto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductPhotoRequest $request, ProductPhoto $productPhoto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPhoto $productPhoto)
    {
        //
    }
}
