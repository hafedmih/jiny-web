<?php

namespace App\Http\Controllers\Taxi\Web\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\OutstationUploadImages;

class ShareTripController extends BaseController
{

    public function requestView($id)
    {
       
    	$requests = RequestModel::where('id',$id)->first();
        
    	return view('taxi.requests.ShareTripView', ['requests' => $requests]);
    }
    
}
