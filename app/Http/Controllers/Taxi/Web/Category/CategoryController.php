<?php

namespace App\Http\Controllers\Taxi\Web\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Taxi\Web\CategorySaveRequest;

use App\Models\taxi\Category;
use App\Models\taxi\Vehicle;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:new-category', ['only' => ['categorySave']]);
        $this->middleware('permission:edit-category', ['only' => ['categoryEdit','categoryUpdate']]);
        $this->middleware('permission:delete-category', ['only' => ['categoryDelete']]);
        $this->middleware('permission:active-category', ['only' => ['categoryChangeStatus']]);
    }

    public function category(Request $request)
    {
        $category = Category::get();

        return view('taxi.category-management.category-management',['category' => $category]);
    }

    public function categorySave(CategorySaveRequest $request)
    {
        $data = $request->all();

        $filename =  uploadImage('images/category',$request->file('category_image'));

        $category = Category::create([
            'category_name' => $data['category_name'],
            'category_image' => $filename,
            'status' => 1,
        ]);

        return response()->json(['message' =>'success'], 200);
    }

    public function categoryEdit($id)
    {
        $category = Category::where('slug',$id)->first();

        return response()->json(['message' =>'success','category' => $category], 200);
    }

    public function categoryDelete($id)
    {
        $category = Category::where('slug',$id)->first();
        // unlink($category->category_image);
        $categorymap = Vehicle::where('category_id',$category->id)->get();
        $data = ['cannot delete'];  
        if(count($categorymap)>0){
            session()->flash('message','Cannot delete the category');
            session()->flash('status',false);
             return back();       
        }else{
            $category = Category::where('slug',$id)->delete();         
            return back();
        }          
        
        return redirect()->route('category');
    }

    public function categoryChangeStatus($id)
    {
        $category = Category::where('slug',$id)->first();

        if($category->status == 1){
            $category->status = 0;
        }
        else{
            $category->status = 1;
        }
        $category->save();
        return redirect()->route('category');
    }

    public function categoryUpdate(CategorySaveRequest $request)
    {
        $data = $request->all();

        $category = Category::where('slug',$data['category_id'])->first();

        $category->category_name = $data['category_name'];
        
        if($data['category_image'] != "undefined"){
            $filename =  uploadImage('images/category',$request->file('category_image'));
            $category->category_image = $filename;
        }

        $category->save();

        return response()->json(['message' =>'success'], 200);

    }
}
