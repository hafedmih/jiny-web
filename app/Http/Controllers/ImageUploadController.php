<?php

  

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\taxi\Requests\TripLog;
  

class ImageUploadController extends Controller

{

     /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function imageUpload()

    {

        return view('imageUpload');

    }

    

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function imageUploadPost(Request $request)

    {

        $request->validate([

            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

    

        $imageName = time().'.'.$request->image->extension();  

        $path = Storage::disk('s3')->put('images', $request->image);

        $path = Storage::disk('s3')->url($path);

  

        /* Store $imageName name in DATABASE from HERE */

    

        return back()

            ->with('success','You have successfully upload image.')

            ->with('image', $path); 

    }

    public function test(){
        $post = new TripLog;

    $post->title = 'test';
    $post->body = 'body';
    $post->slug = 'slug';

    $post->save();

    return response()->json(["result" => "ok"], 201);
    }

}