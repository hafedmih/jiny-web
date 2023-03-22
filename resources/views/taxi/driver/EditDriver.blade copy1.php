@extends('layouts.app')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.179.0.min.js"></script>

<style>
.nav-item a{
    font-weight: bold;
}
.nav-item .nav-link.active{
    background: #ec407a !important;
    color: #fff !important;
}
</style>

<div class="content">
     
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-driver') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a href="{{ route('driver') }}" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-arrow-left7 mr-2"></i> {{ __('back') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-top nav-tabs-bottom nav-justified">
                <li class="nav-item"><a href="#top-justified-tab1" class="nav-link @if($type == 'edit') active @endif" data-toggle="tab">{{ __('edit-driver') }}</a></li>
                <li class="nav-item"><a href="#top-justified-tab2" class="nav-link @if($type == 'document') active @endif" data-toggle="tab">{{ __('manage-driver-document') }}</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade @if($type == 'edit') show active @endif" id="top-justified-tab1">
                    <fieldset class="mb-3">
                        <form method="post" id="roleForm" autocomplete="off">
                            @csrf
                            <input type="hidden" name="slug" id="slug" value="{{$driver->slug}}" />
                            <legend class="text-uppercase font-size-sm font-weight-bold">{{ __('edit-driver') }}</legend>
                            <!-- <div class="alert alert-danger alert-dismissible" id="errorbox"> -->
                                <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                                <!-- <span id="errorContent"></span> -->
                            <!-- </div> -->
                            <div class="row">
                                
                                <div class="form-group row col-md-6 form-group row required "> 
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('first-name') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="{{ __('first-name') }}" value="{{$driver->firstname}}">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 form-group row">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('last-name') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="{{ __('last-name') }}" value="{{$driver->lastname}}">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 form-group row">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('email') }}</label>
                                    <div class="col-lg-9">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="{{ __('email') }}" value="{{$driver->email}}">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 form-group row required">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('phone-number') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="{{ __('phone-number') }}" value="{{$driver->phone_number}}">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 form-group row required">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('country') }}</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="country" id="country">
                                            <option value="">{{ __('country') }}</option>
                                            @foreach($country as $value)
                                                <option value="{{$value->id}}" @if($driver->country == $value->id) selected @elseif($value->code == 'IN') selected @endif>{{$value->name}} ({{$value->dial_code}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 form-group row">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('gender') }}</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="gender" id="gender">
                                            <option value="">{{ __('gender') }}</option>
                                            <option value="male" @if($driver->gender == 'male') selected @endif>{{ __('male') }}</option>
                                            <option value="female" @if($driver->gender == 'female') selected @endif>{{ __('female') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row col-md-6">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('city') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="city" id="city" value="{{$driver->driver ? $driver->driver->city : ''}}" class="form-control" placeholder="{{ __('city') }}">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('state') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="state" value="{{$driver->driver ? $driver->driver->state : ''}}" id="state" class="form-control" placeholder="{{ __('state') }}">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('pincode') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="pincode" id="pincode" value="{{$driver->driver ? $driver->driver->pincode : ''}}" class="form-control" placeholder="{{ __('pincode') }}">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 form-group row required">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('type') }}</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="type" id="type">
                                            <option value="">{{ __('type') }}</option>
                                            @foreach($types as $value)
                                                <option value="{{$value->id}}" @if($driver->driver && $driver->driver->type == $value->id) selected @endif>{{$value->vehicle_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                        <div class="form-group row col-md-6 form-group row vehicle_model required">
                            <label class="col-form-label col-lg-3">{{ __('vehicle_model') }}</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="vehicle_model" id="vehicle_model">
			                        <option value="">{{ __('vehicle_model') }}</option>
                                    @foreach($models as $value)
                                        <option value="{{$value->slug}}" @if($driver->driver && $driver->driver->car_model == $value->model_name) selected @endif>{{$value->model_name}}</option>
                                    @endforeach
                                    <option value="1">Other</option>
		                        </select>
                            </div>
                        </div>
                                <div class="form-group row col-md-6 required">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('car-number') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="car_number" id="car_number" class="form-control" placeholder="{{ __('car-number') }}" value="{{$driver->driver ? $driver->driver->car_number : ''}}">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 vehicle_model_name required">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('car-model') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="car_model" id="car_model" class="form-control" placeholder="{{ __('car-model') }}" value="{{$driver->driver ? $driver->driver->car_model : ''}}">
                                    </div>
                                </div>
                                <!-- <div class="form-group row col-md-6">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('car-year') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="car_year" id="car_year" class="form-control" placeholder="{{ __('car-year') }}" value="{{$driver->driver ? $driver->driver->car_year : ''}}">
                                    </div>
                                </div>
                                <div class="form-group row col-md-6">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('car-colour') }}</label>
                                    <div class="col-lg-9">
                                        <input type="text" name="car_colour" id="car_colour" class="form-control" placeholder="{{ __('car-colour') }}" value="{{$driver->driver ? $driver->driver->car_colour : ''}}">
                                    </div>
                                </div> -->
                        <div class="form-group row required col-md-6">
                            <label class="col-form-label col-lg-3">{{ __('service_type') }}</label>
                            <div class="col-lg-9">
                                <label class="custom-control custom-control-secondary custom-checkbox mb-2">
									<input type="checkbox" class="custom-control-input" name="service_type[]" value="OUTSTATION" id="outstation" @if(in_array("OUTSTATION", $user->driver->service_category)) checked @endif>
									<span class="custom-control-label">{{ __('outstation') }}</span>
								</label>

								<label class="custom-control custom-control-danger custom-checkbox mb-2">
									<input type="checkbox" class="custom-control-input" name="service_type[]" value="RENTAL" id="rental" @if(in_array("RENTAL", $user->driver->service_category)) checked @endif >
									<span class="custom-control-label">{{ __('rental') }}</span>
								</label>

								<label class="custom-control custom-control-success custom-checkbox mb-2">
									<input type="checkbox" class="custom-control-input" name="service_type[]" value="LOCAL" id="local" @if(in_array("LOCAL", $user->driver->service_category)) checked @endif>
									<span class="custom-control-label">{{ __('local') }}</span>
								</label>
                            </div>
                            <label class="col-form-label col-lg-3">{{ __('Notes') }}</label>                            
                            <div class="col-lg-9">
                               <textarea name="notes" id="notes" class="form-control" placeholder="{{ __('notes') }}">{{$driver->driver->notes}}</textarea>
                               
                            </div>
                        </div>
                                <div class="form-group row col-md-12">
                                    <label class="col-form-label col-lg-2 font-weight-bold">{{ __('address') }}</label>
                                    <div class="col-lg-10">
                                        <textarea name="address" id="address" class="form-control" placeholder="{{ __('address') }}">{{$driver->address}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row col-md-6">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('driver-image') }}</label>
                                    <div class="col-lg-9">
                                        <img id="{{$driver->id}}" class="rounded-circle" width="100" height="100" alt="">
                                        <input type="file" name="driver_image" id="driver_image" class="form-control" />
                                    </div>
                                </div>

                                @if($driver->driver ? $driver->driver->subscription_type : '')
                                @if($driver->driver->subscription_type == 'BOTH' || $driver->driver->subscription_type == 'SUBSCRIPTION')
                                <div class="form-group row col-md-6  ">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('subscription') }}</label>
                                    <div class="col-lg-9 ">
                                        <select class="form-control" name="subscription" id="subscription">
                                            <option value="">{{ __('setect_subscription') }}</option>
                                            @foreach($subscription as $value)
                                                <option value="{{$value->slug}}">{{$value->name}} ({{$value->amount}}) - {{$value->validity}} days</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @else
                                <input type="hidden" name="subscription" id="subscription" />
                                @endif
                                @endif
                                <div class="form-group row col-md-6 required">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('category') }}</label>
                                    <div class="form-check mb-0">
                                        <label class="form-check-label">
                                            <input type="radio" name="category" @if($driver->driver->login_method == 'INDIVIDUAL') checked @endif class="form-check-input-styled category" data-fouc value="INDIVIDUAL">
                                           {{ __('individual') }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <label class="form-check-label">
                                            <input type="radio" name="category" @if($driver->driver->login_method == 'COMPANY') checked @endif class="form-check-input-styled category" data-fouc value="COMPANY">
                                            {{ __('company') }}
                                        </label>
                                    </div>
                                </div>
                              <div class="form-group row col-md-6 select_company">
                                    <label class="col-form-label col-lg-3 font-weight-bold">{{ __('company') }}</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" name="company" id="company" onclick="onclick_events()">
                                            <option value="">{{ __('company') }}</option>
                                            @foreach($company as $value)
                                                <option value="{{$value->slug}}" @if($driver->driver && $driver->driver->company_id == $value->id) selected @endif>{{$value->firstname}} {{$value->lastname}}</option>
                                            @endforeach
                                            <option value="1" @if($driver->getDummyCompany) selected @endif>{{ __('others') }}</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group row col-md-6 company_details">
                                    <label class="col-form-label col-lg-3">{{ __('company_name') }}</label>
                                    <div class="col-lg-9">
                                        <input name="company_name" id="company_name" class="form-control" value="{{$driver->getDummyCompany ? $driver->getDummyCompany->company_name : ''}}" placeholder="{{ __('company_name') }}"/>
                                        <input name="company_slug" id="company_slug" value="{{$driver->getDummyCompany ? $driver->getDummyCompany->slug : ''}}" class="form-control" type="hidden"/>
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 company_details">
                                    <label class="col-form-label col-lg-3">{{ __('company_phone') }}</label>
                                    <div class="col-lg-9">
                                        <input name="company_phone" id="company_phone" value="{{$driver->getDummyCompany ? $driver->getDummyCompany->company_phone_number : ''}}" class="form-control" placeholder="{{ __('company_phone') }}"/>
                                    </div>
                                </div>
                                <div class="form-group row col-md-6 company_details">
                                    <label class="col-form-label col-lg-3">{{ __('no_of_vehicle') }}</label>
                                    <div class="col-lg-9">
                                        <input name="no_of_vehicle" id="no_of_vehicle" value="{{$driver->getDummyCompany ? $driver->getDummyCompany->total_no_of_vehicle : ''}}" class="form-control" placeholder="{{ __('no_of_vehicle') }}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                            @if(auth()->user()->can('submit-driver-detail'))
                                <button type="button" id="saveBtn" class="btn btn-primary legitRipple">Submit <i class="icon-paperplane ml-2"></i></button>
                            @endif
                            </div>
                        </form>
                    </fieldset>
                </div>

                <div class="tab-pane fade @if($type == 'document') show active @endif" id="top-justified-tab2">                
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>{{ __('approved') }}</th>
                                <th>{{ __('document-name') }}</th>
                                <th>{{ __('images') }}</th>
                                <th>{{ __('document-status') }}</th>
                                <th>{{ __('denied') }}</th>
                                <th>{{__('expiry status')}}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($document as $key => $docum)
                                <tr>
                                
                                    <td>
                                        @if(auth()->user()->can('approved-driver-document'))
                                        <!-- <input type="checkbox" id="approved_{{$docum->slug}}" name="approved[]" class="" value="{{$docum->slug}}"> -->
                                        @if($docum->document_status != "")
										<label class="form-check-label">
                                            <input type="checkbox" id="approved_{{$docum->slug}}" name="approved[]" class="form-check-input-styled approved" data-fouc value="{{$docum->slug}}">
										</label>
                                        @endif
                                        @endif
                                    </td>
                                    <td>{!! $docum->document_name !!} @if($docum->requried) <span class="text-danger">*</span> @endif </td>
                                    <td>
                                        <figure class="figure">
                                            <img id="{{$docum->slug}}" class="view_image_button" height="40px" width="auto" alt=""  /> 
                                            <figcaption class="figure-caption text-center ">{{ __('click here') }}</figcaption>
                                        </figure>

                                    </td>
                                    <td>                                  
                                        @if($docum->document_status == 2)
                                            <span class="badge badge-primary">{{ __('approved') }}</span>
                                        @elseif($docum->document_status == 0)
                                            <span class="badge badge-danger">{{ __('denied') }}</span>
                                        @elseif($docum->document_status == 1)
                                            <span class="badge bg-pink">{{ __('not-approved') }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ __('not-uploaded') }}</span>
                                        @endif                                        
                                    </td> 
                                    <td>
                                         @if(auth()->user()->can('denait-driver-document'))
                                         @if($docum->document_status != "")
                                         <label class="form-check-label">
                                            <input type="checkbox" id="denait_{{$docum->slug}}" name="denaited[]"  for="denait_{{$docum->slug}}" class="form-check-input-styled denaited" data-fouc value="{{$docum->slug}}">
										</label>
                                        @endif
                                        @endif
                                    </td>
                                    <td>    
                                                                      
                                       @if($docum->expiry_date != '' && $docum->expiry_date != '0000-00-00')
                                            @if($docum->expiry_date <  date('Y-m-d'))
                                                <span class="badge badge-danger">{{ __(' expired') }}</span>
                                            @else
                                                 <span class="badge badge-success">{{ __('not expired') }}</span>   
                                            @endif 
                                        @else
                                          <span class="badge badge-info">{{ __('no data') }}</span>                                      
                                        @endif
                                    </td>
                                     
                                    <td>                     
                                        @if(auth()->user()->can('edit-driver-document'))
                                            <a href="" class="btn bg-pink-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" onclick="Javascript: return updateAction(`../driver-document-edit/{{$user->slug.'/'.$docum->slug}}`)" data-placement="bottom" data-original-title="Edit"> <i class="icon-pencil"></i> </a>
                                        @endif
                                       
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="card-header header-elements-inline">
                        <div class="card-title btn btn-outline-primary btn-lg legitRipple btn-sm">
                            <select class="form-control" name="subscription_type" id="subscription_type">
                            @if($driver->driver ? $driver->driver->subscription_type : '')
                                <option value="">{{ __('select_subscription_type') }}</option>
                                <option value="COMMISSION" @if($driver->driver->subscription_type == 'COMMISSION') selected @endif>Commission</option>
                                <option value="SUBSCRIPTION" @if($driver->driver->subscription_type == 'SUBSCRIPTION') selected @endif>Subscription</option>
                                <option value="BOTH" @if($driver->driver->subscription_type == 'BOTH') selected @endif>Both</option>
                                @endif
                            </select>
                        </div>
                        <div class="header-elements">
                            <div class="list-icons">
                            @if(auth()->user()->can('updated-document'))
                                <button type="button" class="btn btn-primary legitRipple" id="approved" >Updated <i class="icon-paperplane ml-2"></i></button>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="top-justified-tab3">
                    DIY synth PBR banksy irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg whatever.
                </div>

                <div class="tab-pane fade" id="top-justified-tab4">
                    Aliquip jean shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda labore aesthet.
                </div>
            </div>
        </div>
    </div>
    <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" action="{{ route('driverDocumentUpdate') }}" method="post" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox1">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent1"></span>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-form-label">{{ __('title') }}</label>
                                <input type="text" name="title" id="title" class="form-control"  placeholder="{{ __('title') }}" >
                                <input type="hidden" name="document_id" id="document_id">
                                <input type="hidden" name="date_required" id="date_required">
                                <input type="hidden" name="driver_id" id="driver_id" value="{{$user->slug}}">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-form-label">{{ __('image1') }}</label>
                                <input type="file" id="document_image" class="form-control" name="document_image">
                                <img src="{{$docum->document_image}}" id="view_image_2"  id="{{$docum->slug}}"  width="100" height="100" alt="">
                               

                            </div>
                            <div class="form-group col-md-6 dated">
                                <label class="col-form-label date_lable">{{ __('experie-date') }}</label>
                                <input type="date" id="expiry_date" class="form-control" name="expiry_date">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn1" class="btn bg-primary">{{ __('save-changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="roleModel1" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('view-document') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="card">
                    <img src="{{$docum->document_image}}" id="view_image" height="100%" width="auto" alt="" />
                     <img id="{{$docum->slug}}" class="rounded-circle" width="100" height="100" alt="">

                    </div>
                </div>

                <div class="modal-footer">
                    <a href="#" download type="type" id="download" class="btn bg-primary">{{ __('download') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("#errorbox").hide();
    $("#errorbox1").hide();   
    @if(!$driver->driver->type) $(".vehicle_model").hide(); @endif
    $(".vehicle_model_name").hide();
    @if($driver->driver->login_method != 'COMPANY') $(".select_company").hide(); @endif
    @if($driver->driver->login_method != 'COMPANY')  $('.company_details').hide();  @endif
    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var values = new Array();
        $.each($("input[name='service_type[]']:checked"), function() {
            values.push($(this).val());
        });
       // console.log(values);
        var formData = new FormData();
      //  console.log(formData);
        formData.append('driver_image',$('#driver_image').prop('files').length > 0 ? $('#driver_image').prop('files')[0] : '');
        formData.append('first_name',$('#first_name').val());
        formData.append('slug',$('#slug').val());
        formData.append('last_name',$('#last_name').val());
        formData.append('email',$('#email').val());
        formData.append('phone_number',$('#phone_number').val());
        formData.append('country',$('#country').val());
        formData.append('gender',$('#gender').val());
        formData.append('city',$('#city').val());
        formData.append('state',$('#state').val());
        formData.append('pincode',$('#pincode').val());
        formData.append('type',$('#type').val());
        formData.append('vehicle_model',$('#vehicle_model').val());
        formData.append('car_number',$('#car_number').val());
        formData.append('car_model',$('#car_model').val());
        formData.append('car_year',$('#car_year').val());
        formData.append('car_colour',$('#car_colour').val());
        formData.append('subscription',$('#subscription').val());
        formData.append('address',$('#address').val());
        formData.append('company',$('#company').val());
        formData.append('company_name',$('#company_name').val());
        formData.append('company_slug',$('#company_slug').val());
        formData.append('company_phone_number',$('#company_phone').val());
        formData.append('total_no_of_vehicle',$('#no_of_vehicle').val());
        formData.append('service_type',values);
        formData.append('notes',$('#notes').val());
        formData.append('category',$('input[name="category"]:checked').val());
        formData.append('_token',"{!! csrf_token() !!}");
        $(this).html("{{ __('sending') }}");
        $("#errorbox").hide();
        $.ajax({
            data: formData,
            url: "{{ route('driverUpdate') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                if(data.status){
                    swal({
                        title: "{{ __('data-added') }}",
                        text: "{{ __('data-updated-successfully') }}",
                        icon: "success",
                    }).then((value) => {        
                        window.location.href = "{{ route('driver') }}";
                    });
                }
                else{
                    swal({
                        title: "{{ __('error') }}",
                        text: data.message,
                        icon: "error",
                    }).then((value) => {    
                        $('#saveBtn').html("{{ __('Submit') }} <i class='icon-paperplane ml-2'></i>");
                        // window.location.href = "{{ route('driver') }}";
                    });
                }
                        
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                    $('#errorContent').html('');
                if(err.errors){
                    $.each(err.errors, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtn').html("{{ __('save-changes') }}");
                }
                if(!err.status){
                    $('#errorContent').append('<strong><li>'+err.message+'</li></strong>');
                }
            }
        });
    });
</script>

<script type="text/javascript">
    var i =1;
    var approved = [];
    var denaited = [];
    $(".dated").hide();
    $(".hide").hide();
    $(".view_image_button").click(function(){
        var image = $(this).attr('src');
        var image_2 = $(this).attr('src');
        $("#view_image").attr('src',image);
        $("#view_image_2").attr('src',image);
        $("#download").attr('href',image);
        $('#roleModel1').modal('show');
    })

    function updateAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $("#view_image_2").attr('src',"");
                $('#roleForm').trigger("reset");
               //console.log(data);
                $('#modelHeading').html("{{ __('edit-document') }}");
                $('#errorbox1').hide();
                // $('#saveBtn').html("Edit Complaint");
                $('#saveBtn1').val("edit_document");
                $('#roleModel').modal('show');
                $('#document_id').val(data.data.slug);
                $('#title').val(data.data.document_name);
                $('#expiry_date').val(data.data.expiry_dated);
                //$('#image').attr("src",data.data.document_image);
                //console.log(data.data);
                if(data.data.document_image != ""){
                    getUrlByFileName(data.data.document_image, mimes.jpeg).then(function(data) {
                        $("#view_image_2").attr('src',data);
                    });
                }
                if(data.data.expiry_date == 1){
                    $(".dated").show();
                    $('.date_lable').html("{{ __('experie-date') }}");
                    $('#expiry_date').val(data.data.expiry_dated);
                }
                else if(data.data.expiry_date == 2){
                    $(".dated").show();
                    $('.date_lable').html("{{ __('issue-date') }}");
                    $('#expiry_date').val(data.data.issue_dated);
                }
                else{
                    $(".dated").hide();
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    $('#document_image').change(function(){
          let reader = new FileReader();
          reader.onload = (e) => { 
            $("#image").attr('src',e.target.result);
          }
          reader.readAsDataURL(this.files[0]); 
    });

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $(".approved").change(function(){
        var id = $(this).attr('for');
        $("#denait_"+$(this).val()).attr('disabled',false);
        $('input[name="approved[]"]:checkbox:checked').each(function(i){
            approved[i] = $(this).val();
            $("#denait_"+approved[i]).attr('disabled',true);
        });
    });

    
    $("#vehicle_model").change(function(){
        if($(this).val() == '1'){
            $(".vehicle_model_name").show();
        }
        else{
            $(".vehicle_model_name").hide();
        }
    });
    
    $(".category").change(function(){
        if($('input[name="category"]:checked').val() == 'COMPANY'){
            $(".select_company").show();
            $("#company").val('');
        }
        else{
            $(".select_company").hide();
            $('.company_details').hide();
            $("#company").val('0');
            $("#company_name").val('');
            $("#company_phone").val('');
            $("#no_of_vehicle").val('');
        }
    });

    $("#type").on('change',function(){
        var type = $(this).val();
        var text = '';
        
        $(".vehicle_model").show();
        $.ajax({
            url: "{{ url('driver/get/models') }}/"+type,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $.each(data.models, function(key, value) {
                  //  console.log(value);
                    text += '<option value="'+value.slug+'">'+value.model_name+'</option>'
                });
                text += '<option value="1">Other</option>';
                $("#vehicle_model").html(text);
                $(".vehicle_model_name").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
               
            }
        });
    })

    $(".denaited").change(function(){
        var id = $(this).attr('for');
        $("#approved_"+$(this).val()).attr('disabled',false);
        $('input[name="denaited[]"]:checkbox:checked').each(function(i){
            denaited[i] = $(this).val();
            $("#approved_"+denaited[i]).attr('disabled',true);
        });
    });

    $('#saveBtn1').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('document_image',$('#document_image').prop('files').length > 0 ? $('#document_image').prop('files')[0] : '');
        formData.append('document_id',$('#document_id').val());
        formData.append('driver_id',$('#driver_id').val());
        formData.append('date_required',$('#date_required').val());
        formData.append('expiry_date',$('#expiry_date').val());
        $(this).html("{{ __('sending') }}");
        $("#errorbox1").hide();
        $.ajax({
            data: formData,
            url: "{{ route('driverDocumentUpdate') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                swal({
                    title: "{{ __('data-added') }}",
                    text: "{{ __('data-added-successfully') }}",
                    icon: "success",
                }).then((value) => {        
                    window.location.href = "../driver-edit/"+$('#driver_id').val()+"?type=document";
                    
                });
                        
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox1').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err);
                $('#errorContent1').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent1').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn1').html("{{ __('save-changes') }}");
            }
        });
    });



    $('#approved').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('approved_document_id',approved);
        formData.append('denaited_document_id',denaited);
        formData.append('subscription_type',$('#subscription_type').val());
        formData.append('driver_id',$('#driver_id').val());
        $.ajax({
            data: formData,
            url: "{{ route('driverDocumentApproved') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                swal({
                    title: "{{ __('data-updated') }}",
                    text: "{{ __('data-updated-successfully') }}",
                    icon: "success",
                }).then((value) => {        
                    window.location.href = "../driver-edit/"+$('#driver_id').val()+"?type=document";
                });
                        
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox1').show();
                var err = eval("(" + xhr.responseText + ")");
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn1').html("{{ __('save-changes') }}");
            }
        });
    });
</script>
<script src="{{ asset('backend/assets/js/jquery.multiselect.js') }}"></script>
<script type="text/javascript">
    function onclick_events()
    {
        var company = $('#company').val();
        if(company == 1){
            $('.company_details').show();

        }else{
            $('.company_details').hide();
        }
    }

</script>

<!--Driver Photo view-->

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

    

        if("{{$driver->profile_pic}}" != ""){
       
          getUrlByFileName('{{$driver->profile_pic}}', mimes.jpeg).then(function(data) {
    
        $("#{{$driver->id}}").attr('src',data);
      });
      }
   


  </script>

<!--All Document view-->

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

        @foreach($document as $key => $docum)
        if("{{$docum->document_image}}" != ""){
          getUrlByFileName('{{$docum->document_image}}', mimes.jpeg).then(function(data) {
        $("#{{$docum->slug}}").attr('src',data);
      });
      }
      @endforeach
   


  </script>



  <!--All Document view-->

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

        @foreach($document as $key => $docum)
        if("{{$docum->document_image}}" != ""){
          getUrlByFileName('{{$docum->document_image}}', mimes.jpeg).then(function(data) {
        $("#{{$docum->slug}}").attr('src',data);
      });
      }
      @endforeach
   


  </script>


<script type="text/javascript">
    var i =1;
    var message = "{{session()->get('message')}}";

    if(message){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
            location.reload();
        });
    }

</script>


@endsection
