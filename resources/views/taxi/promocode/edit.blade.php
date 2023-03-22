@extends('layouts.app')

@section('content')
<style>
    .form-group.required .require:after{
                content:" *";
                color: red;
                weight:100px;
            }
            /* .require:after{
                content:" *";
                color: red;
                weight:100px;
            } */

</style>
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">



<div class="content">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('promo-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a href="{{ route('promolist') }}" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-arrow-left7 mr-2"></i> back</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Form inputs -->
    <div class="card">
					
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
	    <div class="card-body">
            <form action="{{ route('PromoUpdate') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="promo_slug" value="{{$promolist->slug}}" />
            @csrf
                <div class="row">
	                <div class="col-md-4">
						<div class="form-group required">
							<label class="require">{{ __('zone') }} </label>
                            <select id="service_location" class="form-control" name="zone_id" disabled>
			                    <option value="">{{ __('select-zone')}}</option>
                                @foreach($zone as $key => $zones)
			                    <option value="{{ $zones->id}}" @if($promolist->zone_id == $zones->id) selected @endif>{{ $zones->zone_name}}</option>
                                @endforeach
	                        </select>  
						</div>
	            	</div>
	        		<div class="col-md-4">
                        <div class="form-group required">
							<label class="require">{{ __('promo-code')}}</label>
                            <input type="text" value="{{$promolist->promo_code}}" class="form-control" autocomplete="off"  id="promocode" name="promo_code"  placeholder="{{ __('enter-your-promo-code')}}" disabled>
                            <br>                        
                            <button type="button" id="btnSubmit" class="btn btn-info " disabled>{{ __('generate-promocode') }}</button>
						</div>
	        		</div>
	                <div class="col-md-4">
						<div class="form-group required">
							<label class="require">{{ __('select_offer_option') }}</label>
                            <select id="select_offer_option" class="form-control" name="select_offer_option" disabled>
                                <option value="0">{{ __('select_offer_option') }}</option>
			                    <option value="1" @if($promolist->select_offer_option == "1") selected @endif>{{ __('new_user_promo') }}</option>
			                    <option value="2" @if($promolist->select_offer_option == "2") selected @endif>{{ __('distance_based_promo')}}</option>
			                    <option value="3" @if($promolist->select_offer_option == "3") selected @endif>{{ __('amount_based_promo')}}</option>
		                        <option value="4" @if($promolist->select_offer_option == "4") selected @endif>{{ __('new_launch_promo')}}</option>
		                        <option value="5" @if($promolist->select_offer_option == "5") selected @endif>{{ __('individual_promo')}}</option>
			                </select>  
                        </div>
	                </div>
	            </div>
                <div class="row" >
                    <div class="col-md-4">
						<div class="form-group required">
							<label class="require">{{ __('promo_icon')}}</label>
                            <input type="file" class="form-control" id="promo_icon" name="promo_icon" disabled >
                            <img src="{{$promolist->promo_icon}}" style="width:100px;height:auto" />
						</div>
	            	</div>
                    <div class="col-md-8">
						<div class="form-group">
							<label class="require">{{ __('promo_description')}}</label>
                            <textarea class="form-control"  rows="1" name="description">{{$promolist->description}}</textarea>
						</div>
	            	</div>
                </div>
                <div class="row">
                    <div class="col-md-4" id="newuser_promo_type">
                        <div class="form-group required">
                            <label class="require">{{ __('target_amount')}} </label>
                            <input type="text" class="form-control" name="target_amount"  placeholder="Enter your Amount" value="{{$promolist->target_amount}}" >
                        </div>
                    </div>
                    <div class="col-md-4" id="newuser_promo_type">
                        <div class="form-group required">
                            <label class="require">{{ __('promo_type')}} </label>
                            <select id="new_promo_type" class="form-control" name="promo_type"   onclick="onclick_promotype();">
                                <option value="">{{ __('select_promoType')}}</option>
                                <option value="1" @if($promolist->promo_type == "1") selected @endif>{{ __('fixed')}}</option>
                                <option value="2" @if($promolist->promo_type == "2") selected @endif>{{ __('percentage')}}</option>
                            </select>  
                        </div>
                    </div> 
                    <!-- <div class="col-md-4">
                        <div class="form-group required">
                            <label class="col-form-label require">{{ __('types') }}</label>
                            <select id="types_id" class="form-control" multiple="multiple" name="types[]">
                                @foreach($vehicleList as $values)
			                    <option value="{{$values->slug}}" @if(in_array($values->id,$promolist->types)) selected @endif>{{$values->vehicle_name}}</option>
                                @endforeach
			                </select>
                        </div>
                    </div> -->
                    <div class="col-md-4" id="amount_1">
                        <div class="form-group">
                            <label>{{ __('amount')}}</label>
                            <input type="text" class="form-control" name="amount" value="{{$promolist->amount}}" placeholder="{{ __('enter_your_amount')}}">
                        </div>
                    </div>
                    <div class="col-md-4" id="percentage_1">
                        <div class="form-group">
                            <label>{{ __('percentage')}}</label>
                            <input type="text" class="form-control" name="percentage" value="{{$promolist->percentage}}" placeholder="{{ __('enter_your_percentage')}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="require">{{ __('promo_use_count')}}</label>
                            <input type="text" class="form-control" name="promo_use_count" value="{{$promolist->promo_use_count}}" placeholder="{{ __('promo_use_count')}}">
                        </div>
                    </div>
                    <div class="col-md-4 single_user_count">
                        <div class="form-group">
                            <label class="require">{{ __('promo_user_reuse_count')}}</label>
                            <input type="text" class="form-control" name="promo_user_reuse_count" value="{{$promolist->promo_user_reuse_count}}" placeholder="{{ __('promo_user_reuse_count')}}">
                        </div>
                    </div>
                     <!-- <div class="col-md-4">
                        <div class="form-group">
                            <label class="require">{{ __('total_promo_limit_count')}}</label>
                            <input type="text" class="form-control" value="{{$promolist->total_promo_limit_count}}" name="total_promo_limit_count" placeholder="{{ __('total_promo_limit_count')}}">
                        </div>
                    </div> -->
                </div>
                <div id="festival_based_promo"  style="display: none;">
                    <div class="row">
	                	<div class="col-md-4" id="from_date">
							<div class="form-group">
								<label>From date </label>
                                <input type="date" class="form-control" value="{{$promolist->from_date}}" name="from_date">
							</div>
	                	</div>
	                	<div class="col-md-4" id="to_date">
                            <div class="form-group">
								<label>To date </label>
                                <input type="date" class="form-control" value="{{$promolist->to_date}}" name="to_date">
							</div>
	                	</div>
	                </div>
                </div>
                <div id="new_user_based_promo"  style="display: none;">
                    <div class="row">
	                	<div class="col-md-4" id="from_date">
							<div class="form-group">
								<label>New User Count </label>
                                <input type="text" class="form-control" value="{{$promolist->new_user_count}}" placeholder="New User Count" name="new_user_count">
							</div>
	                	</div>
	                </div>
                </div>
                <div id="individual_based_promo" style="display: none;">
                    <div class="row">
                        <div class="col-md-4" id="users_id">
							<div class="form-group">
								<label>Users </label>
                                <select class="form-control" name="user_id[]" id="driver_id" multiple="multiple">
                                    @foreach($user as $key => $users)
			                        <option value="{{ $users->id}}" @if($promolist->UsersList && in_array($users->id,$promolist->UsersList)) selected @endif>{{ $users->firstname}}</option>
                                    @endforeach
			                    </select>  
							</div>
	                	</div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary legitRipple">Submit <i class="icon-paperplane ml-2"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->

<script type="text/javascript">

$(document).ready(function() {

    onclick_promotype();
    select_offer_option();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#btnSubmit").click(function(){
        var text = "";

        $.ajax({
            url: "{{ URL::Route('generatePromo') }}",
            type:"GET",
            dataType: 'json',
            success: function (data) {
                $('#promocode').val(data.promo);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var err = eval("(" + xhr.responseText + ")");      
            }
            })
    });
    
});


function select_offer_option() {
    if ($("#select_offer_option").val() == "1")
    {
        $('#individual_based_promo').hide();
        $('#festival_based_promo').hide();
        $('#new_user_based_promo').show();
        $(".single_user_count").hide();
    } 
    else if ($("#select_offer_option").val() == "2")
    {
        $('#individual_based_promo').show();
        $('#festival_based_promo').hide();
        $('#new_user_based_promo').hide();
        $(".single_user_count").show();
    }
    else if ($("#select_offer_option").val() == "3")
    {
        $('#individual_based_promo').show();
        $('#festival_based_promo').hide();
        $(".single_user_count").show();
    }
    else if ($("#select_offer_option").val() == "4")
    {
        $('#festival_based_promo').show();
        $('#individual_based_promo').hide();
        $('#new_user_based_promo').hide();
        $(".single_user_count").show();
    }
    else if ($("#select_offer_option").val() == "5")
    {
        $('#individual_based_promo').show();
        $('#festival_based_promo').hide();
        $('#new_user_based_promo').hide();
        $(".single_user_count").show();
    }
    else{
        $('#individual_based_promo').hide();
        $('#festival_based_promo').hide();
        $('#new_user_based_promo').hide();
        $(".single_user_count").show();
    }
};

    // Promo Type Select Option

    function onclick_promotype()
    {
        var promo_type = $('#new_promo_type').val();
        if(promo_type == 1){
           // console.log('general so disappeared!');
            $('#percentage_1').hide();
            $('#amount_1').show();

        }
        else if(promo_type == 2)
        {
            //console.log('offer upto so appeared');
            $('#percentage_1').show();
            $('#amount_1').hide();
           
        }
    }


</script>


<script src="{{ asset('backend/assets/js/jquery.multiselect.js') }}"></script>
<script>
$('#driver_id').multiselect({
    columns: 1,
    placeholder: 'Select User List',
    selectAll: true,
    search: true,
});
$('#types_id').multiselect({
    columns: 1,
    placeholder: 'Select Types List',
    selectAll: true,
    search: true,
});
</script>

@endsection
