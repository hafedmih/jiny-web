<?php

namespace App\Http\Controllers\Taxi\Web\InvoiceQuestions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\taxi\InvoiceQuestions;
use App\Models\taxi\Vehicle;

use Validator;
use Redirect;

class InvoiceQuestionsController extends Controller
{


    public function InvoiceQuestions(Request $request)
    {
        $vehicleList = InvoiceQuestions::all();
        $vehicle = Vehicle::all();
        return view('taxi.vehicle-model.vehicle',['vehicleList' => $vehicleList,'vehicle' => $vehicle]);
    }

    

    public function index(Request $request)
    {
        $questions = InvoiceQuestions::all();
        

        return view('taxi.invoice-questions.invoice',['questions' => $questions]);
    }

    public function questionsStore(Request $request)
    {
        $data = $request->all();
        $count = InvoiceQuestions::count();
        if($count < 5){
            $vehicleadd = InvoiceQuestions::create([
                'questions' => $data['questions'],
            ]);
        }else{
            session()->flash('message',"Questions Limit exceeded");
        }

        return response()->json(['message' =>'success'], 200);
    }

    public function questionsEdit($id)
    {
        $questions = InvoiceQuestions::where('slug',$id)->first();

        return response()->json(['message' =>'success','questions' => $questions], 200);
    }

    public function questionsUpdate(Request $request)
    {
        $data = $request->all();

        $questions = InvoiceQuestions::where('slug',$data['questions_id'])->update([
            'questions' => $data['questions'],
        ]);

       
            return response()->json(['message' =>'success'], 200);
    }

    public function questionsDelete($id)
    {
        $questions = InvoiceQuestions::where('slug',$id)->first();
        $questions = InvoiceQuestions::where('slug',$id)->delete();
        return back();
    }

    public function questionsStatusChange($id)
    {
        $data = InvoiceQuestions::where('slug',$id)->first();
        if($data->status == 1){
            $data->status = 0;
        }
        else{
            $data->status = 1;
        }
        $data->save();
        
        return back();
    }
    
}