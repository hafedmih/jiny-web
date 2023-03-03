@extends('layouts.dispatcher-layout')

@section('content')
<style>
    .autocompletes{
        z-index: 9999;
        position: absolute;
        background: #fff;
        border: solid 1px #000;
        height:auto;
        max-height: 250px;
        overflow: hidden;
        overflow-y: scroll;
    }
    .autocompletes ul{
        padding: 0px;
    }
    .autocompletes ul li{
        list-style: none;
        padding: 10px;
        font-size: 15px;
    }
    .autocompletes ul li:hover{
        cursor: pointer;
        background: #52abeb;
        color: #fff;
    }
    .list-group-item{
        line-height: 1;
    }
    .clickCheck{
        display:none;
    }

    .popup{
    width: 900px;
    margin: auto;
    text-align: center
    }

    .popup img{
        width: 75px;
        height: 100px;
        cursor: pointer
    }

    .show{
        z-index: 999;
        display: none;
    }

    .show .overlay{
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.66);
        position: absolute;
        top: 0;
        left: 0;
    }

    .show .img-show{
        width: 600px;
        height: 400px;
        background: #FFF;
        position: absolute;
        top: 25%;
        right: 20%;
        transform: translate(-50%,-50%);
        overflow: hidden
    }
    .img-show span{
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 99;
        cursor: pointer;
    }
    .img-show img{
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

    .popup1{
    width: 900px;
    margin: auto;
    text-align: center
    }

    .popup1 img{
        width: 75px;
        height: 100px;
        cursor: pointer
    }

    .show1{
        z-index: 999;
        display: none;
    }

    .show1 .overlay1{
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.66);
        position: absolute;
        top: 0;
        left: 0;
    }

    .show1 .img-show1{
        width: 600px;
        height: 400px;
        background: #FFF;
        position: absolute;
        top: 25%;
        right: 40%;
        transform: translate(-50%,-50%);
        overflow: hidden
    }
    .img-show1 span{
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 99;
        cursor: pointer;
    }
    .img-show1 img{
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

     .text-red {
     color : #f44336 !important;
     font-weight :bold;
     }

</style>

<div class="content">
    <div class="alert bg-danger text-white alert-styled-left alert-dismissible">
		<!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
		<span class="font-weight-semibold">Sorry!</span> <span id="error_message"></span>.
	</div>
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <div class="card card-body">
                <h5 class="card-title">{{ __('user_details')}}</h5> 
                <div class="media">
                    <div class="media-body">
                        <div class="media-title font-weight-semibold"><a style="color:#222" href="{{ route('userView',$request->userDetail->slug)}}">{{$request->userDetail ? $request->userDetail->firstname : ''}} {{$request->userDetail ? $request->userDetail->lastname : ''}}</a></div>
                        <span class="text-muted">{{$request->userDetail ? $request->userDetail->phone_number : ''}}</span><br>
                        <span class="text-muted">{{$request->userDetail ? $request->userDetail->email : ''}}</span>
                    </div>
                    <div class="ml-3">
                        <img src="{{$request->userDetail && $request->userDetail->profile_pic ? $request->userDetail->profile_pic : asset('backend/global_assets/images/demo/users/face6.jpg') }}" width="72" height="72" alt="" id="user_image">
                    </div>
                </div>
            </div>
        </div>
        @if($request->driverDetail)
        <div class="col-xl-4 col-md-6">
            <div class="card card-body">
                <h5>{{ __('driver_details')}}</h5>
                <div class="media">
                    <div class="media-body">
                        <div class="media-title font-weight-semibold " ><a style="color:#222" href="{{ route('driverDetails',$request->driverDetail->slug)}}">{{$request->driverDetail->firstname}} {{$request->driverDetail->lastname}}</a></div>
                        <span class="text-muted">{{$request->driverDetail->phone_number}}</span><br>
                        <span class="text-muted">{{$request->driverDetail->email}}</span>
                    </div>
                    <div class="ml-3">
                        <img src="{{$request->driverDetail->profile_pic ? $request->driverDetail->profile_pic : asset('backend/global_assets/images/demo/users/face6.jpg') }}" width="72" height="72" alt="" id="driver_image">
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-xl-4 col-md-6">
            <div class="card card-body">
                <div class="header-elements-inline">
					<h5 class="card-title">{{ __('trip_details')}}</h5>
					<!-- <div class="header-elements">
                        @if($request->is_cancelled == 0 && $request->is_driver_started == 0 && !$request->driverDetail)
                            <div class="list-icons">
                                <button class="btn btn-success btn-sm rounded-pill" onclick="driverSearch('{{ route('searchDriver',$request->id) }}')">Driver search</button>
                            </div>
                        @endif
				    </div> -->
				</div>
                <div class="media">
                    <div class="media-body">
                        <div class="media-title font-weight-semibold" >{{ __('invoice')}}<a style="color:#222" href="{{ route('requestView',$request->id) }}"> {{$request->request_number}}</a></div>
                        <span class="text-muted">Date : {{date("d-m-Y",strtotime($request->created_at))}}</span><br>
                        <span class="text-muted">Time : {{date("h:i:s A",strtotime($request->created_at))}}</span><br>
                        <span class="text-muted text-red">OTP : {{$request->request_otp}}</span>
                    </div>
                    <div class="ml-3">
                            Status: &nbsp;
                            @if($request->is_cancelled == 1)
                                <label class="badge bg-danger-400 align-top">{{ __('cancelled')}}</label><br>
                                <span class="text-danger">{{ $request->cancel_method }}<br>{{$request->cancellationRequest  && $request->cancellationRequest->resonDetails ? $request->cancellationRequest->resonDetails->reason : ''}}</span>
                            @elseif($request->is_completed == 1)
                                <label class="badge bg-success-400 align-top">{{ __('completed')}}</label>
                            @elseif($request->is_trip_start == 1)
                                <label class="badge bg-warning-400 align-top">{{ __('trip_started')}}</label>
                            @elseif($request->is_driver_arrived == 1)
                                <label class="badge bg-info-400 align-top">{{ __('driver_arrived')}}</label>
                            @elseif($request->is_driver_started == 1)
                                <label class="badge bg-info-400 align-top">{{ __('driver_accepted')}}</label>
                            @else
                                <label class="badge bg-primary-400 align-top">{{ __('trip_created')}}</label>
                            @endif
                             
                            <br>
                            Method : {{$request->ride_type}} 
                            <br>
                            Category : {{$request->trip_type}}
                           
                    </div>
                </div>
            </div>
        </div>
        @if(!$request->driverDetail && !$request->is_cancelled)
        <div class="col-xl-4 col-md-6">
            <div class="card card-body">
                <h5>{{ __('assign_driver')}}</h5>
                <!-- <div class="row">
                    <div class="form-group col-md-12">
                        <select name="vehicle_type" onchange="myFunction1()" id="vehicle_type" class="form-control required">
                            <option value="">Select Type</option>
                            @if($request->trip_type == "LOCAL")
                                @foreach($types as $key => $value)
                                    <option value="{{$value->slug}}">{{$value->vehicle_name}}</option>
                                @endforeach
                            @elseif($request->trip_type == "RENTAL")
                                @foreach($request->getPackage->getPackageItems as $key => $value)
                                    <option value="{{$value->getVehicle->slug}}">{{$value->getVehicle->vehicle_name}}</option>
                                @endforeach
                            @else
                                @foreach($outstations as $key => $value)
                                    <option value="{{$value->getVehicle->slug}}">{{$value->getVehicle->vehicle_name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div> -->
                <div class="row">
                    <div class="form-group col-md-9">
                        <label>Driver Number: <span class="text-danger">*</span></label>
                        <input type="number" name="customer_number" onkeyup="myFunction()" id="customer_number" placeholder="Driver Number" class="form-control required" onKeyPress="if(this.value.length==10) return false;">
                        <input type="hidden" name="driver_id" id="driver_id"  class="form-control">
                        <div class="autocompletes">
                            <ul id="autocompletes">
                                                
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3"><br><button id="assign" class="btn btn-success">{{ __('assign')}}</button></div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-6" >
            <div id="map-canvas" style="width:100%;height:100%;"></div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">{{ __('location_details')}}</h6>
                            <div class="header-elements">
                                <!-- <a href="#">All updates</a> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="media-list">
                                @if($request->requestPlace)
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn bg-transparent text-primary border-primary text-primary rounded-round border-2 btn-icon legitRipple">
                                            <i class="icon-location3"></i>
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h6>{{$request->requestPlace->pick_address}}</h6>
                                        <!-- <div class="text-muted font-size-sm">12 minutes ago</div> -->
                                    </div>
                                </li>
                                @if($request->requestPlace->stop_address != '')
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn bg-transparent text-warning border-warning text-warning rounded-round border-2 btn-icon legitRipple">
                                            <i class="icon-location3"></i>
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h6>{{$request->requestPlace->stop_address}}</h6>
                                        <!-- <div class="text-muted font-size-sm">12 minutes ago</div> -->
                                    </div>
                                </li>
                                @endif
                                @if($request->requestPlace->drop_address != '')
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn text-danger bg-transparent border-danger text-primary rounded-round border-2 btn-icon legitRipple">
                                            <i class="icon-location3"></i>
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h6>{{$request->requestPlace->drop_address}}</h6>
                                        <!-- <div class="text-muted font-size-sm">12 minutes ago</div> -->
                                    </div>
                                </li>
                                @endif
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">{{ __('trip_time_details')}}</h6>
                            <div class="header-elements">
                                <!-- <a href="#">All updates</a> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="media-list">
                                @if($request->trip_start_time && !$request->is_driver_started)
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn bg-transparent border-success text-success rounded-pill border-2 btn-icon legitRipple"><i class="icon-checkmark3"></i></a>
                                    </div>              
                                    <div class="media-body">
                                        <span class="font-weight-semibold my-2">{{ __('trip_booking_time')}}</span>
                                        <div class="text-muted">{{$request->trip_start_time}}</div>
                                    </div>
                                </li>  
                                @if($request->trip_end_time) 
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn bg-transparent border-success text-success rounded-pill border-2 btn-icon legitRipple"><i class="icon-checkmark3"></i></a>
                                    </div>              
                                    <div class="media-body">
                                        <span class="font-weight-semibold my-2">{{ __('trip_ending_time')}}</span>
                                        <div class="text-muted">{{$request->trip_end_time}}</div>
                                    </div>
                                </li>   
                                @endif    
                                @endif    
                                @if($request->accepted_at)
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn bg-transparent border-success text-success rounded-pill border-2 btn-icon legitRipple"><i class="icon-checkmark3"></i></a>
                                    </div>              
                                    <div class="media-body">
                                        <span class="font-weight-semibold my-2">{{ __('trip_accepted_time')}}</span>
                                        <div class="text-muted">{{$request->accepted_at}}</div>
                                    </div>
                                </li>   
                                @endif    
                                @if($request->arrived_at) 
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn bg-transparent border-success text-success rounded-pill border-2 btn-icon legitRipple"><i class="icon-checkmark3"></i></a>
                                    </div>              
                                    <div class="media-body">
                                        <span class="font-weight-semibold my-2">{{ __('trip_arrived_time')}}</span>
                                        <div class="text-muted">{{$request->arrived_at}}</div>
                                    </div>
                                </li>          
                                @endif    
                                @if($request->trip_start_time && $request->is_driver_started && $request->arrived_at)                    
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn bg-transparent border-success text-success rounded-pill border-2 btn-icon legitRipple"><i class="icon-checkmark3"></i></a>
                                    </div>                          
                                    <div class="media-body">
                                        <span class="font-weight-semibold my-2">{{ __('trip_start_time')}}</span>
                                        <div class="text-muted">{{$request->trip_start_time}}</div>
                                    </div>
                                </li>     
                                @endif    
                                @if($request->completed_at) 
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn bg-transparent border-success text-success rounded-pill border-2 btn-icon legitRipple"><i class="icon-checkmark3"></i></a>
                                    </div>              
                                    <div class="media-body">
                                        <span class="font-weight-semibold my-2">{{ __('trip_completed_time')}}</span>
                                        <div class="text-muted">{{$request->completed_at}}</div>
                                    </div>
                                </li>   
                                @endif    
                                @if($request->is_cancelled) 
                                <li class="media">
                                    <div class="mr-3">
                                        <a href="#" class="btn bg-transparent border-danger text-danger rounded-pill border-2 btn-icon legitRipple"><i class="icon-cross"></i></a>
                                    </div>              
                                    <div class="media-body">
                                        <span class="font-weight-semibold my-2">{{ __('trip_cancelled_time')}}</span>
                                        <div class="text-muted">{{$request->cancelled_at}}</div>
                                    </div>
                                </li>    
                                @endif     
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        @if($outstation_trip)
                            <div class="row">
                                @if($outstation_trip->trip_start_km_image)
                                    <div class=" col-md-6">
                                        <div class=" card-body">
                                            <h6>{{ __('trip_start') }}</h6>
                                            <span>{{$outstation_trip->trip_start_km}}</span>
                                            <div class="media">
                                                <div class="popup">
                                                    <img src="{{$outstation_trip->trip_start_km_image  }}" class="view_image_button" width="75px" height="100px" alt="">
                                                </div>
                                                <div class="show">
                                                    <div class="overlay"></div>
                                                    <div class="img-show">
                                                        <span style="color:red">X</span>
                                                        <img src="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($outstation_trip->trip_end_km_image)
                                    <div class=" col-md-6">
                                        <div class=" card-body">
                                            <h6>{{ __('trip_end') }}</h6>
                                            <span>{{$outstation_trip->trip_end_km}}</span>
                                            <div class="media">
                                                <div class="popup1">
                                                    <img src="{{ $outstation_trip->trip_end_km_image }}" class="view_image_button" width="75px" height="100px" alt="">
                                                </div>
                                                <div class="show1">
                                                    <div class="overlay1"></div>
                                                    <div class="img-show1">
                                                        <span style="color:red">X</span>
                                                        <img src="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class=" card-body">
                                    <span>{{ __('distance') }}</span>    <strong>{{$outstation_trip->distance}}</strong>      
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    @if($request->driverDetail && $request->driverDetail->driver && $request->driverDetail->driver->vehicletype)
                        <div class="card card-body">
                            <h5>{{ __('vehicle_type_details')}}</h5>
                            <div class="media">
                                <div class="media-body">
                                    <div class="media-title font-weight-semibold">{{$request->driverDetail->driver->vehicletype->vehicle_name}}</div>
                                    <div class="media-title">{{$request->driverDetail->driver->car_number}}</div>
                                    <div class="media-title">{{$request->driverDetail->driver->car_model}}</div>
                                </div>
                                <div class="ml-3">
                                    <img src="{{$request->driverDetail->driver->vehicletype->image ? $request->driverDetail->driver->vehicletype->image : asset('backend/global_assets/images/demo/users/face6.jpg') }}" width="72" height="72" alt="">
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card card-body">
                        <h5>Driver Notes</h5>
                        <div class="media">
                            <div class="media-body">
                                <p class="media-title font-weight-semibold">{{$request->driver_notes}}</p>
                            </div>
                        </div>
                    </div>
                    @if(!$request->is_cancelled)
                        <div class="card">
                            <div class="card-header header-elements-inline">
                                <h6 class="card-title">{{ __('trip_bill')}}</h6>
                                <div class="header-elements">
                                    <!-- <a href="#">All updates</a> -->
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <ul class="list-group">
                                    @if($request->requestBill == Null && $request->getAssignAmount && $request->getAssignAmount->request_amount || $request->requestBill == Null && $request->getAssignAmount && $request->getAssignAmount->amount_per_km)
                                    <li class="list-group-item">
                                        Fixed Amount <span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->getAssignAmount->request_amount ? $request->getAssignAmount->request_amount : $request->getAssignAmount->amount_per_km}}</span></li>
                                    @else
                                    <li class="list-group-item">
                                        @if($request->trip_type != "OUTSTATION"){{ __('base_price') }}@else{{ __('distance_price') }}@endif <span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->base_price : ($request->is_later == 0 ? ($request->getZonePrice ? $request->getZonePrice->ridenow_base_price : ($request->outstationPriceDetails ? $request->outstationPriceDetails->distance_price : $request->getPackageItem->price)) : ($request->getZonePrice ? $request->getZonePrice->ridelater_base_price : ($request->outstationPriceDetails ? $request->outstationPriceDetails->distance_price : "0.00")))}}</span></li>
                                    @endif
                                    @if($request->requestBill != Null)
                                        @if($request->requestBill->price_per_distance != '0.00')<li class="list-group-item">{{ __('price_per_distance')}}<span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->price_per_distance : '0.00'}}</span></li>@endif
                                        @if($request->requestBill->total_distance != '0.00')<li class="list-group-item">Distance  <span class="ml-auto">{{$request->requestBill ? $request->requestBill->total_distance : '0.00'}} Km</span></li>@endif
                                        @if($request->requestBill->distance_price != '0.00')<li class="list-group-item">Distance Price <span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->distance_price : '0.00'}}</span></li>@endif
                                        @if($request->requestBill->price_per_time != '0.00')<li class="list-group-item">Ride Time Charge <span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->price_per_time : '0.00'}}</span></li>@endif
                                        @if($request->requestBill->waiting_charge != '0.00')<li class="list-group-item">Waiting Charge <span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->waiting_charge : '0.00'}}</span></li>@endif
                                        @if($request->requestBill->out_of_zone_price != '0.00')<li class="list-group-item">{{ __('out_of_zone_fees') }}<span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->out_of_zone_price : '0.00'}}</span></li>@endif
                                        @if($request->requestBill && $request->requestBill->booking_fees != '0.00')<li class="list-group-item">Booking Fee<span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->booking_fees : '0.00'}}</span></li>@endif
                                        @if($request->requestBill && $request->requestBill->promo_discount != '0.00')<li class="list-group-item">Promo Discount<span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->promo_discount : '0.00'}}</span></li>@endif
                                        <li class="list-group-item text-danger">Roda Commission<span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->admin_commision : '0.00'}}</span></li>
                                        <li class="list-group-item text-danger">Service Tax<span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->service_tax : '0.00'}}</span></li>
                                        <li class="list-group-item list-group-divider"></li>
                                        <li class="list-group-item">Total <span class="ml-auto">{{$request->requested_currency_symbol}} {{$request->requestBill ? $request->requestBill->total_amount : '0.00'}}</span></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
                @if((is_array($request->requestQuestion) ? count($request->requestQuestion) : 0) > 0)
                <div class="col-lg-12">
                    <!-- List of files -->
                    <div class="card card-collapsed">
                        <div class="card-header bg-transparent header-elements-inline">
                            <h6 class="card-title font-weight-semibold">
                                <i class="icon-folder6 mr-2"></i>
                                Requestions Answer
                            </h6>
                            <div class="header-elements">
								<div class="list-icons">
				            		<a class="list-icons-item  rotate-180" data-action="collapse"></a>
			                	</div>
			            	</div>
                            <!-- <div class="header-elements">
                                <span class="text-muted">(93)</span>
                            </div> -->
                        </div>

                        <div class="list-group list-group-flush">
                            @foreach($request->requestQuestion as $key => $value)
                            <a href="#" class="list-group-item list-group-item-action">
                                <!-- <i class="icon-file-pdf mr-3"></i> -->
                                <span class=" mr-1">{{$key+1}}.</span>
                                {{$value->questionDetails ? $value->questionDetails->questions : ''}}
                                @if($value->answer == "YES")
                                    <!-- <span class="badge bg-success-400 ml-auto"> {{$value->answer}} </span> -->
                                    <i class="icon-thumbs-up2 text-success ml-auto"></i>
                                @else
                                    <!-- <span class="badge bg-danger-400 ml-auto">{{$value->answer}} </span> -->
                                    <i class="icon-thumbs-down2 text-danger ml-auto"></i>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                    <!-- /list of files -->
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->

<script type="text/javascript">
    $(".autocompletes").hide();
    $(".bg-danger").hide();
    var i =1;
    var message = "{{session()->get('message')}}";

    if(message){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

    // function driverSearch(url){
    //     // console.log(url);
    //     $.ajax({
    //         url: url,
    //         type: "GET",
    //         dataType: 'json',
    //         success: function (data) {
    //             // console.log(data);
    //             if(data.success){
    //                 if(data.data.hold_status){
    //                     swal({
    //                         title: "{{ __('errors') }}",
    //                         text: data.message,
    //                         icon: "error",
    //                     }).then((value) => {        
    //                         // window.location.href = "../driver-document/"+$('#driver_id').val();
    //                     });
    //                 }
    //                 else{
    //                     swal({
    //                         title: "{{ __('success') }}",
    //                         text: data.message,
    //                         icon: "success",
    //                     }).then((value) => {        
    //                         // window.location.href = "../driver-document/"+$('#driver_id').val();
    //                     });
    //                 }
    //             }
    //         },
    //         error: function (xhr, ajaxOptions, thrownError) {
    //             var err = eval("(" + xhr.responseText + ")");
    //             console.log(err);
    //             console.log('2');
    //         }
    //     });
    // }

    function myFunction() {
        var value = $("#customer_number").val();
        var value = $("#customer_number").val();
        var text = "";
        if(value != ""){
            $.ajax({
                url: "{{ url('get-driver-detail') }}/{{$request->id}}/"+value,
                type: "GET",
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if(data.data.length > 0){
                        $.each( data.data, function( key, value ) {
                            text += "<li id='"+value.slug+"' class='getCustomer' >"+value.firstname+" "+value.lastname+" ("+value.phone_number+")</li>"
                        });
                        $(".autocompletes").show();
                        $("#autocompletes").html(text);
                    }
                    else{
                        $(".autocompletes").hide();
                    }
                    
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    var err = eval("(" + xhr.responseText + ")");
                    $(".bg-danger").show();
                    $("#error_message").text(err.message);
                    setTimeout(function(){ $(".bg-danger").fadeOut(3000); }, 10000);
                }
            });
        }
    }

    $(document).on('click',".getCustomer",function(){
        var id = $(this).attr('id');
        $.ajax({
            url: "{{ url('get-customer-detail') }}/"+id,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $("#customer_number").val(data.data.phone_number);
                $("#driver_id").val(data.data.id);
                $(".autocompletes").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                
            }
        });
    })

    $(document).on('click','#assign',function(){
        var slug = $("#driver_id").val();

        $.ajax({
            url: "{{ url('assign-driver-trip') }}/{{$request->id}}/"+slug,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                if(data.success){
                    location.reload();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                
            }
        });
    })

</script>
<script>
    var pick_place_id = '';
    var drop_place_id = '';
    var stopPlaceId = [];
    var map = '';
    @if($request->requestPlace)
        function initMap() {
            map = new google.maps.Map(document.getElementById('map-canvas'), {
                center: {
                    lat: {{$request->requestPlace->pick_lat}},
                    lng: {{$request->requestPlace->pick_lng}}
                },
                zoom: 13
            });
            @if($request->requestPlace->drop_lat == "")
                const infoWindow = new google.maps.InfoWindow();
                var image = '{{ asset("backend/point2.png") }}';
                var markers = new google.maps.Marker({
                    position: { lat: {{$request->requestPlace->pick_lat}}, lng: {{$request->requestPlace->pick_lng}} },
                    map,
                    animation: google.maps.Animation.DROP,
                    title: "{{$request->requestPlace->pick_address}}" 
                });
                markers.addListener("click", () => {
                    infoWindow.setContent("{{$request->requestPlace->pick_address}}");
                    infoWindow.open(markers.getMap(), markers);
                });
                map.setZoom(12);
                map.setCenter(markers.getPosition());
            @else
                travelMode = google.maps.TravelMode.DRIVING;
                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setMap(map);

                // const image = "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png";
                // const beachMarker = new google.maps.Marker({
                //         position: { lat: {{$request->requestPlace->pick_lat}}, lng: {{$request->requestPlace->pick_lng}} },
                //         map,
                //         // icon: image,
                //         title: "{{$request->requestPlace->pick_address}}",
                //     });
                
                var geocoder = new google.maps.Geocoder;
                var latlng = { lat: {{$request->requestPlace->pick_lat}}, lng: {{$request->requestPlace->pick_lng}} };

                geocoder.geocode({'location': latlng}, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        pick_place_id = results[1].place_id;
                        this.route();
                    } else {
                        window.alert('No results found');
                    }
                    } else {
                    window.alert('Geocoder failed due to: ' + status);
                    }
                });
                if({{$request->requestPlace->drop_lat}}){
                    var geocoder = new google.maps.Geocoder;
                    var latlng = { lat: {{$request->requestPlace->drop_lat}}, lng: {{$request->requestPlace->drop_lng}} };

                    geocoder.geocode({'location': latlng}, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            drop_place_id = results[1].place_id;
                            this.route();
                        } else {
                            window.alert('No results found');
                        }
                        } else {
                        window.alert('Geocoder failed due to: ' + status);
                        }
                    });
                }
            @endif
        }

        // class AutocompleteDirectionsHandler {
        function route() {
            if (!pick_place_id || !drop_place_id) {
                return;
            }
            var stop_address = "{{$request->requestPlace->stop_address}}";
            if(stop_address){
            stopPlaceId.push({
                    location: stop_address,
                    stopover: true,
                });
            }

            const me = this;
            if(stop_address != ""){
                var request = {
                    origin: { placeId: pick_place_id },
                    destination: { placeId: drop_place_id },
                    waypoints: stopPlaceId,
                    travelMode: travelMode,
                };
            }
            else{
                var request = {
                    origin: { placeId: pick_place_id },
                    destination: { placeId: drop_place_id },
                    travelMode: travelMode,
                };
            }
            
            directionsService.route(request,
                (response, status) => {
                    if (status === "OK") {
                    me.directionsRenderer.setDirections(response);
                    } else {
                    window.alert("Directions request failed due to " + status);
                    }
                });
        }
        @endif
    // }

    
</script>

<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAwpTPjHhnVfQuq37V-Gc322b42qTKS-Io&libraries=drawing,places&callback=initMap" async defer>     -->

<script src="https://maps.googleapis.com/maps/api/js?key={{settingValue('geo_coder')}}&libraries=drawing,places,geometry&callback=initMap" async defer>    
</script>
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-database.js"></script>
<!-- TODO: Add SDKs for Firebase products that you want to use https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-analytics.js"></script>
<script type="text/javascript">
     $(function () {
    "use strict";
    
    $(".popup img").click(function () {
        var $src = $(this).attr("src");
        $(".show").fadeIn();
        $(".img-show img").attr("src", $src);
    });

    $(".popup1 img").click(function () {
        var $src = $(this).attr("src");
        $(".show1").fadeIn();
        $(".img-show1 img").attr("src", $src);
    });
    
    $("span, .overlay1").click(function () {
        $(".show1").fadeOut();
    });
    $("span, .overlay").click(function () {
        $(".show").fadeOut();
    });
    
});
    </script>

<script type="text/javascript">

    var showFreeDrivers = true;
    var showUnAvailableDrivers = true;

    var heatmapData = [];
    var pickLat = [];
    var pickLng = [];
    var default_lat = '11.0176052';
    var default_lng = '76.9586527';
    var driverLat,driverLng,bearing,type;
    var marker = [];
    


    const firebaseConfig = {
        apiKey: "{{settingValue('firebase_api_key')}}",
        authDomain: "{{settingValue('firebase_auth_domain')}}",
        databaseURL: "{{settingValue('firebase_database_url')}}",
        projectId: "{{settingValue('firebase_project_id')}}",
        storageBucket: "{{settingValue('firebase_storage_bucket')}}",
        messagingSenderId: "{{settingValue('firebase_messaging_sender_id')}}",
        appId: "{{settingValue('firebase_app_id')}}",
        measurementId: "{{settingValue('firebase_measurement_id')}}"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        firebase.analytics();
        // var num = 10;
        var tripRef = firebase.database().ref('requests');
        tripRef.on('value', async function(snapshot) {
            // console.log(num);
            // num++;
            // if(num < 10){
            //     return false;
            // }
            // num = 0;
            var data = snapshot.val();
            // console.log(data);
            await loadDriverIcons(data);
        });

        // map = new google.maps.Map(document.getElementById('map'), {
        //     center: new google.maps.LatLng(default_lat, default_lng),
        //     zoom: 9,
        //     mapTypeId: 'roadmap'
        // });
        // directionsDisplay = new google.maps.DirectionsRenderer();
        // directionsService = new google.maps.DirectionsService();

        var iconBase = '{{ asset("backend") }}';
        var icons = {
          available: {
            name: 'Available',
            key: 'free',
            // icon: iconBase + '/point2.png'
            icon: iconBase + '/car.png'
          },
          ontrip: {
            name: 'OnTrip',
            key: 'ontrip',
            // icon: iconBase + '/point2.png'
            icon: iconBase + '/car.png'
          }
        };

        // var legend = document.getElementById('legend');

        // for (var key in icons) {
        //     var type = icons[key];
        //     var name = type.name;
        //     var icon = type.icon;
        //     var div = document.createElement('div');
        //     div.innerHTML = `<input type="checkbox" id="${name}" name="${name}" value="${type.name}" onchange='legendChanged("${type.key}")' checked> <img src="${icon}"> <b>${name}</b> <br><br><br>`;
        //     legend.appendChild(div);
        // }

        // map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);

        function loadDriverIcons(data){



          deleteAllMarkers();
          // var result = Object.entries(data);
          Object.entries(data).forEach(([key, val]) => {  
              // var infowindow = new google.maps.InfoWindow({
              //     content: contentString
              // });
            //   console.log(val.request_id);
              if(val.request_id == "{{$request->id}}" && val.driver_trip_status < 3){
                var typeOfFirstName = typeof val.first_name;

                if( typeOfFirstName == 'string' ) {
                    var firstName  = val.first_name+' '+val.last_name;
                }else {
                    var firstName  = '-';
                }

                var typeOfPhone = typeof val.phone_number;
                if( typeOfPhone == "string" ) {
                    var phone  = val.phone_number;
                }else {
                    var phone  = '-';
                }

                // var phone = '+91 7200704057';


                var contentString = '<div id="content">' +
                '<div id="siteNotice">' +
                '</div>' +
                '<h3 id="firstHeading" class="firstHeading"> <i class="fa fa-id-card" aria-hidden="true"></i> &nbsp&nbsp'+ firstName +'&nbsp&nbsp</h3>' +
                '<h4 id="bodyContent" style="color:#4d5051"> <i class="fa fa-phone" aria-hidden="true"></i> &nbsp&nbsp&nbsp&nbsp' + phone +'&nbsp&nbsp'+
                '</h4>' +
                '</div>';

                var infowindow = new google.maps.InfoWindow({
                content: contentString
                });

                // if( typeof val.lat_lng_array !=  'undefined'  ) {
                var iconImg = '';


                if(val.is_available == true){
                    iconImg = icons['available'].icon;
                }else{
                    iconImg = icons['ontrip'].icon;
                }

                var date = new Date();
                var timestamp = date.getTime();
                var currentTime = +new Date(timestamp - 1 * 60000);
                
                //   console.log(val);

                // if(val.is_available == true && showFreeDrivers == true && val.is_active == true ) {

                    var carIcon = new google.maps.Marker({
                        position: new google.maps.LatLng(val.lat,val.lng),
                        icon : {
                            url: iconImg, // url
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(40, 40)

                            // scaledSize: new google.maps.Size(50, 50), // scaled size
                            // origin: new google.maps.Point(0,0), // origin
                            // anchor: new google.maps.Point(0, 0) // anchor
                        },
                        map: map
                        
                    });
                    
                    carIcon.addListener('click', function() {
                        infowindow.open(map, carIcon);

                        // alert( val.first_name );
                        // infowindow.open(map, beachMarker);
                    });


                    // deleteAllMarkers();
                    marker.push(carIcon);
                    carIcon.setMap(map);

                // }else if(val.is_available == false && showUnAvailableDrivers == true ) {


                //     var carIcon = new google.maps.Marker({
                //         position: new google.maps.LatLng(val.l[0],val.l[1]),
                //         icon : {
                //             url: iconImg, // url
                //             origin: new google.maps.Point(0, 0),
                //             anchor: new google.maps.Point(17, 34),
                //             scaledSize: new google.maps.Size(45, 30)
                //             // scaledSize: new google.maps.Size(40, 40), // scaled size
                //             // origin: new google.maps.Point(0,0), // origin
                //             // anchor: new google.maps.Point(0, 0) // anchor
                //         },
                //         map: map,
                    
                //     });

                //     carIcon.addListener('click', function() {
                //         infowindow.open(map, carIcon);
                //     });

                //     // deleteAllMarkers();
                    
                //     marker.push(carIcon);
                //     carIcon.setMap(map);

                // }

                
                // }
            }


          });
        }
        function deleteAllMarkers() {
            for(var i=0;i<marker.length;i++){
                marker[i].setMap(null);
            }
        }

</script>
<script>

    var mimes = {
        'jpeg': 'data:image/jpeg;base64,'
    };

      AWS.config.update({
          signatureVersion: 'v4',
          region: '{{ env('AWS_DEFAULT_REGION') }}',
          accessKeyId: '{{ env('AWS_ACCESS_KEY_ID') }}',
          secretAccessKey: '{{ env('AWS_SECRET_ACCESS_KEY') }}'
      });

      var bucket = new AWS.S3({params: {Bucket: '{{ env('AWS_BUCKET') }}'}});

      function encode(data)
      {
          var str = data.reduce(function(a,b){ return a+String.fromCharCode(b) },'');
          return btoa(str).replace(/.{76}(?=.)/g,'$&\n');
      }

      function getUrlByFileName(fileName,mimeType) {
          return new Promise(
              function (resolve, reject) {
                  bucket.getObject({Key: fileName}, function (err, file) {
                      var result =  mimeType + encode(file.Body);
                      resolve(result)
                  });
              }
          );
         
      }

      function openInNewTab(url) {
          var redirectWindow = window.open(url, '_blank');
          redirectWindow.location;
      }
      if("{{$request->userDetail ? $request->userDetail->profile_pic : ''}}" != ""){
       
       getUrlByFileName('{{$request->userDetail ? $request->userDetail->profile_pic : ''}}', mimes.jpeg).then(function(data) {
           $("#user_image").attr('src',data);
       });
   }

        if("{{$request->driverDetail ? $request->driverDetail->profile_pic : ''}}" != ""){
       
          getUrlByFileName('{{$request->driverDetail ? $request->driverDetail->profile_pic : ''}}', mimes.jpeg).then(function(data) {
    
        $("#driver_image").attr('src',data);
      });
      }


  </script>

<!-- <script src="https://apis.mapmyindia.com/advancedmaps/api/eab7d5cae02147918164985738222231/map_sdk?layer=vector&v=2.0&callback=initMap1" defer async></script>

<script>
    var map;
            function initMap1(){
                map = new MapmyIndia.Map('map-canvas', {
                    center: [28.61, 77.23],
                    zoomControl: true,
                    location: true,
                    search: false
                    // backgroundColor:"red",
                });
            }
        </script> -->

@endsection
