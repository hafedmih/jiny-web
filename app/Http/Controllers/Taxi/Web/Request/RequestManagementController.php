<?php

namespace App\Http\Controllers\Taxi\Web\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\taxi\Requests\Request as RequestModel;


use Validator;


class RequestManagementController extends Controller
{
    public function index()
    {
        $requests = RequestModel::where('is_later', 0)->get();
        $request = RequestModel::where('is_later', 1)->get();
       
        return view('taxi.request-management.index',['request' => $request,'requests' => $requests]);
        
    }

}