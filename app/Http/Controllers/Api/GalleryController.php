<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $gallery = Gallery::where('products_id',$product->id)->get();
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
    public function store(Request $request,Products $product)
    {
        $this->validate_parents($product);

        $input = $request->all();
		$validator = Validator::make($input, [
            'image' => 'required'
		]);
        
        if($validator->fails()){
            return response()->json(["error" => $validator->errors()], 400);
		}

        $input['products_id'] = $product->id;
        $gallery = Gallery::create($input);
		
        return response()->json($gallery, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    //public function show($id)
    public function show($productId,$galleryId)
    {
        $gallery = Gallery::where([
            ['products_id', '=', $productId],
            ['id', '=', $galleryId],
        ])->firstOrFail();

        return $gallery;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(Gallery $gallery)
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
    public function update(Request $request,Products $product, $galleryId)
    {
        $this->validate_parents($product);

        $gallery = $this->show($product->id,$galleryId);

        $gallery->update($request->all());
        return response()->json($gallery, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Products $product, $galleryId)
    {
        $this->validate_parents($product);
        
        $gallery = $this->show($product->id,$galleryId);
        $gallery->delete();

        return response()->json(null, 204);
    }

    private function validate_parents(Products $product){
        if (empty($product)) {
            return response()->json(["Error" => true , "message" => "parents not found"], 404);
        }
    }
}
