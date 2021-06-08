<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Products $product)
    {
        $this->validate_parents($product);
        $gallery = Gallery::where('products_id', $product->id)->get();
        foreach ($gallery as &$img) {
            $img['image'] = $this->get_url_image($img['image']);
        }
        return $gallery;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Products $product)
    {
        $this->validate_parents($product);

        $input = $request->all();

        $validator = Validator::make($input, [
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 400);
        }

        if (!$input['image']->isValid()) {
            return response()->json(["Error" => true, "message" => "there was a problem uploading the image."], 500);
        }

        $path =  $input['image']->store('gallery', 'public');
        $gallery = Gallery::create( [ "products_id" => $product->id, "image" => $path ] );
        $gallery->image = $this->get_url_image($path);
        return response()->json($gallery, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    //public function show($id)
    public function show($productId, $galleryId)
    {
        $gallery = Gallery::where([
            ['products_id', '=', $productId],
            ['id', '=', $galleryId],
        ])->firstOrFail();
        $gallery->image = $this->get_url_image($gallery->image);

        return $gallery;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy(Products $product, $galleryId)
    {
        $this->validate_parents($product);

        $gallery = Gallery::where([
            ['products_id', '=', $product->id],
            ['id', '=', $galleryId],
        ])->firstOrFail();

        Storage::disk('public')->delete($gallery->image);
        $gallery->delete();

        return response()->json(null, 204);
    }

    private function get_url_image($path){
        return asset(Storage::url($path));
    }

    private function validate_parents(Products $product)
    {
        if (empty($product)) {
            return response()->json(["Error" => true, "message" => "parents not found"], 404);
        }
    }
}
