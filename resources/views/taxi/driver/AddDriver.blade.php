@extends('layouts.app')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">

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
            <fieldset class="mb-3">
                <form method="post" id="roleForm"  autocomplete="off">
                    @csrf
                    <legend class="text-uppercase font-size-sm font-weight-bold">{{ __('add-driver') }}</legend>
                    <div class="alert alert-danger alert-dismissible" id="errorbox">
                        <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                        <span id="errorContent"></span>
                    </div>
                    <div class="row">
                        <div class="form-group row col-md-6 form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('first-name') }}</label>
                            <div class="col-lg-9 ">
                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder="{{ __('first-name') }}">
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('last-name') }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder="{{ __('last-name') }}">
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row">
                            <label class="col-form-label col-lg-3">{{ __('email') }}</label>
                            <div class="col-lg-9">
                                <input type="email" name="email" id="email" class="form-control" placeholder="{{ __('email') }}">
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('phone-number') }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="{{ __('phone-number') }}">
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('country') }}</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="country" id="country">
			                        <option value="">{{ __('country') }}</option>
			                        @foreach($country as $value)
			                            <option value="{{$value->id}}" @if($value->code == 'IN') selected @endif>{{$value->name}} ({{$value->dial_code}})</option>
                                    @endforeach
		                        </select>
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row">
                            <label class="col-form-label col-lg-3">{{ __('gender') }}</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="gender" id="gender">
			                        <option value="">{{ __('gender') }}</option>
			                        <option value="male">{{ __('male') }}</option>
			                        <option value="female">{{ __('female') }}</option>
		                        </select>
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-form-label col-lg-3">{{ __('city') }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="city" id="city" class="form-control" placeholder="{{ __('city') }}">
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-form-label col-lg-3">{{ __('state') }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="state" id="state" class="form-control" placeholder="{{ __('state') }}">
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-form-label col-lg-3">{{ __('pincode') }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="pincode" id="pincode" class="form-control" placeholder="{{ __('pincode') }}">
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('type') }}</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="type" id="type">
			                        <option value="">{{ __('type') }}</option>
                                    @foreach($types as $value)
			                            <option value="{{$value->id}}">{{$value->vehicle_name}}</option>
                                    @endforeach
		                        </select>
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row vehicle_model required">
                            <label class="col-form-label col-lg-3">{{ __('vehicle_model') }}</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="vehicle_model" id="vehicle_model">
			                        <option value="">{{ __('vehicle_model') }}</option>
		                        </select>
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('car-number') }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="car_number" id="car_number" class="form-control" placeholder="{{ __('car-number') }}">
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row required vehicle_model_name">
                            <label class="col-form-label col-lg-3">{{ __('car-model') }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="car_model" id="car_model" class="form-control" placeholder="{{ __('car-model') }}">
                            </div>
                        </div>
                        <!-- <div class="form-group row col-md-6 form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('car-year') }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="car_year" id="car_year" class="form-control" placeholder="{{ __('car-year') }}">
                            </div>
                        </div>
                        <div class="form-group row col-md-6 form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('car-colour') }}</label>
                            <div class="col-lg-9">
                                <input type="text" name="car_colour" id="car_colour" class="form-control" placeholder="{{ __('car-colour') }}">
                            </div>
                        </div> -->
                        <div class="form-group row required col-md-6">
                            <label class="col-form-label col-lg-3">{{ __('service_type') }}</label>
                            <div class="col-lg-9">
                                <!-- <label class="custom-control custom-control-secondary custom-checkbox mb-2">
									<input type="checkbox" class="custom-control-input" name="service_type[]" value="OUTSTATION" id="outstation">
									<span class="custom-control-label">{{ __('outstation') }}</span>
								</label> -->

								<label class="custom-control custom-control-danger custom-checkbox mb-2">
									<input type="checkbox" class="custom-control-input" name="service_type[]" value="RENTAL" id="rental" >
									<span class="custom-control-label">{{ __('rental') }}</span>
								</label>

								<label class="custom-control custom-control-success custom-checkbox mb-2">
									<input type="checkbox" class="custom-control-input" name="service_type[]" value="LOCAL" id="local">
									<span class="custom-control-label">{{ __('local') }}</span>
								</label>
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-form-label col-lg-3">{{ __('address') }}</label>
                            <div class="col-lg-9">
                                <textarea name="address" id="address" class="form-control" placeholder="{{ __('address') }}"></textarea>
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-form-label col-lg-3">{{ __('notes') }}</label>
                            <div class="col-lg-9">
                               <textarea name="notes" id="notes" class="form-control" placeholder="{{ __('notes') }}"></textarea>
                            </div>
                        </div>
                        <div class="form-group row col-md-6">
                            <label class="col-form-label col-lg-3 font-weight-bold">{{ __('driver-image') }}</label>
                            <div class="col-lg-9">
                                <input type="file" name="driver_image" id="driver_image" class="form-control" />
                            </div>
                        </div>
                        @if(!auth()->user()->Hasrole('Company'))
                        <div class="form-group row col-md-6 required">
                            <label class="col-form-label col-lg-3 font-weight-bold">{{ __('category') }}</label>
                            <div class="form-check mb-0">
                                <label class="form-check-label">
                                    <input type="radio" name="category" class="form-check-input-styled category" data-fouc value="INDIVIDUAL">
                                    Individual
                                </label>
                            </div>
                            <div class="form-check mb-0">
                                <label class="form-check-label">
                                    <input type="radio" name="category" class="form-check-input-styled category" data-fouc value="COMPANY">
                                    Company
                                </label>
                            </div>
                        </div>
                        @endif
                        
                        @if(auth()->user()->Hasrole('Company'))
                        <div hidden class="form-group row col-md-6 required">
                            <label class="col-form-label col-lg-3 font-weight-bold">{{ __('category') }}</label>
                            <div class="form-check mb-0">
                                <label class="form-check-label">
                                    <input type="radio" name="category" class="form-check-input-styled category " data-fouc value="COMPANY" checked>
                                  
                                </label>
                            </div>
                        </div>
                        <div hidden class="form-group row col-md-6 ">
                            <label class="col-form-label col-lg-3">{{ __('company') }}</label>
                            <div class="col-lg-9">
			                        @foreach($companylogin as $value)
			                            <option name="company" id="company"  value="{{$value->slug}}">{{$value->companyDetails->company_name}} </option>
                                    @endforeach
                            </div>
                        </div>
                        @endif
                        @if(!auth()->user()->Hasrole('Company'))
                        <div class="form-group row col-md-6 select_company">
                            <label class="col-form-label col-lg-3">{{ __('company') }}</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="company" id="company" onclick="onclick_events()">
			                        <option value="0">{{ __('company') }}</option> 
			                        @foreach($company as $value)
                                  
			                            <option value="{{$value->slug}}">{{$value->companyDetails->company_name}} </option>
                                    @endforeach
                                    <option value="1">{{ __('others') }}</option>
		                        </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row col-md-6 company_details">
                            <label class="col-form-label col-lg-3">{{ __('company_name') }}</label>
                            <div class="col-lg-9">
                                <input name="company_name" id="company_name" class="form-control" placeholder="{{ __('company_name') }}"/>
                            </div>
                        </div>
                        <div class="form-group row col-md-6 company_details">
                            <label class="col-form-label col-lg-3">{{ __('company_phone') }}</label>
                            <div class="col-lg-9">
                                <input name="company_phone" id="company_phone" class="form-control" placeholder="{{ __('company_phone') }}"/>
                            </div>
                        </div>
                        <div class="form-group row col-md-6 company_details">
                            <label class="col-form-label col-lg-3">{{ __('no_of_vehicle') }}</label>
                            <div class="col-lg-9">
                                <input name="no_of_vehicle" id="no_of_vehicle" class="form-control" placeholder="{{ __('no_of_vehicle') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
						<button type="button" id="saveBtn" class="btn btn-primary legitRipple">Submit <i class="icon-paperplane ml-2"></i></button>
					</div>
                </form>
			</fieldset>
        </div>
    </div>

</div>

<script>
    $("#errorbox").hide();
    $(".select_company").hide();
    $(".vehicle_model").hide();
    $(".vehicle_model_name").hide();
    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var values = new Array();
        $.each($("input[name='service_type[]']:checked"), function() {
            values.push($(this).val());
        });
        var formData = new FormData();
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
        formData.append('address',$('#address').val());
        formData.append('notes',$('#notes').val());
        formData.append('company',$('#company').val());
        formData.append('company_name',$('#company_name').val());
        formData.append('company_phone_number',$('#company_phone').val());
        formData.append('total_no_of_vehicle',$('#no_of_vehicle').val());
        formData.append('service_type',values);
        formData.append('category',$('input[name="category"]:checked').val() ? $('input[name="category"]:checked').val() : '');
        formData.append('_token',"{!! csrf_token() !!}");
        $(this).html("{{ __('sending') }}");
        $("#errorbox").hide();
        $.ajax({
            data: formData,
            url: "{{ route('driverSave') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                if(data.status){
                    swal({
                        title: "{{ __('data-added') }}",
                        text: "{{ __('data-added-successfully') }}",
                        icon: "success",
                    }).then((value) => {        
                        window.location.href = "{{ route('driver') }}";
                    });
                }
                else{
                    swal({
                        title: "{{ __('errors') }}",
                        text: data.message,
                        icon: "error",
                    }).then((value) => {        
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
                else if(!err.status){
                    $('#errorContent').append('<strong><li>'+err.message+'</li></strong>');
                }
            }
        });
    });
</script>
<script src="{{ asset('backend/assets/js/jquery.multiselect.js') }}"></script>
<script type="text/javascript">
    $('.company_details').hide();
    function onclick_events()
    {
        var company = $('#company').val();
        if(company == 1){
            $('.company_details').show();

        }else{
            $('.company_details').hide();
        }
    }
    
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
                    console.log(value);
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

</script>
@endsection
