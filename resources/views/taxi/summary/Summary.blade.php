@extends('layouts.app')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">
<style>
    table.dataTable tbody td {
        word-break: break-word; white-space: normal;
    }
    label {
        margin-top: 6px;
    }
</style>

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('driver_summary') }}</h5>
            
        </div>
       
    </div>

    <div class="card">
        <div class="card-body row">
            <div class="col-lg-11" >
                <form action="{{ route('SummaryView') }}" method="get">
                    <div class="form-group row">
                        <div class="col-lg-2">
                            <div class=" form-group row ">
                                <div class="col-sm-5">
                                    <label>{{ __('start_date')}}</label>
                                </div>
                                <div class="col-sm-7">
                                    <input type="date"  name="start_date" class="form-control" value="{{$request->start_date}}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <div class=" form-group row ">
                                <div class="col-sm-5">
                                    <label>{{ __('end_date')}}</label>
                                </div>
                                <div class="col-sm-7">
                                    <input type="date"  name="end_date"  class="form-control" value="{{$request->end_date}}">
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-lg-2">
                            <select name="customer" id="customer" class="form-control">
                                    <option value="">{{ __('select_customer')}}</option>
                                    @foreach($customer_list as $key => $customer)
                                    <option value="{{$customer->id}}" @if($request->customer == $customer->id) selected @endif>{{$customer->firstname }} {{ $customer->lastname}}</option>
                                    @endforeach
                                </select>     
                        </div>
                        <div class="col-lg-2">
                                <select name="driver" id="driver" class="form-control">
                                    <option value="">{{ __('select_driver')}}</option>
                                    @foreach($driver_list as $key => $value)
                                    <option value="{{$value->id}}" @if($request->driver == $value->id) selected @endif>{{$value->firstname.' '.$value->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select name="trip_type" id="trip_type" class="form-control">
                                    <option value="">{{ __('select_trip_type')}}</option>
                                    <option value="LOCAL" @if($request->trip_type == 'LOCAL') selected @endif>{{ __('local') }}</option>
                                    <!--<option value="RENTAL" @if($request->trip_type == 'RENTAL') selected @endif>{{ __('rental') }}</option>
                                     <option value="OUTSTATION" @if($request->trip_type == 'OUTSTATION') selected @endif>{{ __('outstation') }}</option> -->
                                </select>
                            </div>
                            
                        <div class="col-lg-2" >
                            <button type="submit" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-filter4 mr-2"></i> {{ __('filter') }}</button>
                        </div>
                        
                    </div>
                </form>
            </div>
            <div class="col-lg-1" >
                <a href="{{route('SummaryView')}}" class="nav-link " style="padding-top: 0px; padding-bottom: 0px;">
                    <button class="btn btn-outline bg-danger-400 text-dark-600 border-danger-300 legitRipple">
                    <i class="icon-reset"></i></button>
                </a>
            </div>
        </div>
    </div>

    <!-- <div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card card-body bg-grey-400">
				<div class="media">
					<div class="align-self-center">
						<i class="icon-piggy-bank icon-3x"></i>
					</div>
					<div class="media-body text-right">
						<h3 class="font-weight-semibold mb-0">{{auth()->user()->getCountry ? auth()->user()->getCountry->currency_symbol : '₹'}} {{$requests_admin_commission}}</h3>
						<span class="text-uppercase font-size-sm">Admin Commission</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card card-body bg-danger-400">
				<div class="media">
					<div class="mr-3 align-self-center">
						<i class="icon-wrench3 icon-3x"></i>
					</div>
					<div class="media-body text-right">
						<h3 class="font-weight-semibold mb-0">{{auth()->user()->getCountry ? auth()->user()->getCountry->currency_symbol : '₹'}} {{$requests_service_tax}}</h3>
						<span class="text-uppercase font-size-sm">Service Tax </span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card card-body bg-primary-400">
				<div class="media">
					<div class="mr-3 align-self-center">
						<i class="icon-cash3 icon-3x"></i>
					</div>
					<div class="media-body text-right">
						<h3 class="font-weight-semibold mb-0">{{auth()->user()->getCountry ? auth()->user()->getCountry->currency_symbol : '₹'}} {{$requests_driver_earning}}</h3>
						<span class="text-uppercase font-size-sm">Driver Earning </span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card card-body bg-success-400">
				<div class="media">
					<div class="mr-3 align-self-center">
						<i class="icon-wallet icon-3x"></i>
					</div>
					<div class="media-body text-right">
						<h3 class="font-weight-semibold mb-0">{{auth()->user()->getCountry ? auth()->user()->getCountry->currency_symbol : '₹'}} {{$requests_total_amount}}</h3>
						<span class="text-uppercase font-size-sm">Total Amount</span>
					</div>
				</div>
			</div>
		</div>
	</div> -->

    <div class="card" id="tableDiv">
        <div class="card-body">
		    <div class="tab-content">
				<div class="tab-pane fade show active" id="justified-right-icon-tab1">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('Request Number') }}</th>
                                <th>{{ __('driver_name') }}</th> 
                                <th>{{  __('Travel Operator')}}</th>
                                <th>{{  __('Service Type')}}</th>
                                <th>{{ __('Customer Name') }}</th>
                                <th>{{ __('Contact Number') }}</th>
                                <th>{{ __('Start Time') }}</th>
                                <th>{{ __('Start Location') }}</th>
                                <th>{{ __('End Time') }}</th>
                                <th>{{ __('End Location') }}</th>
                                <th>{{ __('Base Price') }}</th>
                                <th>{{ __('Waiting Charges') }}</th>
                                <th>{{ __('Promo Bonus') }}</th>
                                <th>{{ __('Service Tax') }}</th>
                                <th>{{ __('Distance Charges') }}</th>
                                <th>{{ __('Total Charges') }}</th>
                                <th>{{ __('Driver Amount') }}</th>
                                <th>{{ __('Company Service Charge') }}</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $key => $request)
                            
                            @if($request->request_id)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{$request->request_number}}</td>
                                    <td>
                                        <a style="color:#222" href="{{ route('driverDetails', $request->driverDetail ? $request->driverDetail->slug : '' )}}">
                                        {!! $request->driverDetail ? $request->driverDetail->firstname.' '.$request->driverDetail->lastname : '' !!}
                                        </a>
                                    </td>
                                    <td></td>
                                    <td>{{ $request->trip_type }}</td>
                                    <td>{!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}</td>
                                    <td>{!! $request->userDetail ? $request->userDetail->phone_number : '' !!}</td>
                                    <td>{!! $request->trip_start_time ? $request->trip_start_time : '' !!}</td>
                                    <td>{!! $request->pick_address ? $request->pick_address : '' !!}</td>
                                    <td>{!! $request->completed_at ? $request->completed_at : '' !!}</td>
                                    <td>{!! $request->drop_address ? $request->drop_address : '' !!}</td>
                                    <td>{!! $request->requested_currency_symbol ? $request->requested_currency_symbol : '' !!}{!! $request->base_price ? $request->base_price : '0.00' !!}</td>
                                    <td>{!! $request->requested_currency_symbol ? $request->requested_currency_symbol : '' !!}{!! $request->waiting_charge ? $request->waiting_charge : '0.00' !!}</td>
                                    <td>{!! $request->requested_currency_symbol ? $request->requested_currency_symbol : '' !!}{!! $request->promo_discount ? $request->promo_discount : '0.00' !!}</td>
                                    <td>{!! $request->requested_currency_symbol ? $request->requested_currency_symbol : '' !!}{!! $request->service_tax ? $request->service_tax : '0.00' !!}</td>
                                    <td>{!! $request->requested_currency_symbol ? $request->requested_currency_symbol : '' !!}{!! $request->distance_price ? $request->distance_price : '0.00' !!}</td>
                                    <td>{!! $request->requested_currency_symbol ? $request->requested_currency_symbol : '' !!}{!! $request->total_amount ? $request->total_amount : '0.00' !!}</td>
                                    <td>{!! $request->requested_currency_symbol ? $request->requested_currency_symbol : '' !!}{!! $request->driver_commision ? $request->driver_commision : '0.00' !!}</td>
                                    <td>{!! $request->requested_currency_symbol ? $request->requested_currency_symbol : '' !!}{!! $request->admin_commision ? $request->admin_commision : '0.00' !!}</td>
                                </tr>
                                @endif
                            @endforeach
                            
                        </tbody>
                        <tfoot>
		                 <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th>₹{{$requests_base_price}}</th><th>₹{{$requests_waiting_charges}}</th><th>₹{{$requests_promo_discount}}</th><th>₹{{$requests_service_tax}}</th><th>₹{{$requests_distance_price}}</th><th>₹{{$requests_total_amount}}</th><th>₹{{$requests_driver_earning}}</th><th>₹{{$requests_admin_commission}}</th></tr>
	                    </tfoot>
                       
                    </table>
				</div>
               
        </div>
    </div>

</div>
<!-- Horizontal form modal -->
<div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">Change Request Category</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <form id="roleForm" name="roleForm" action="{{ route('requestCategoryChange') }}" method="post" class="form-horizontal">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-form-label">{{ __('request_id') }}</label>
                                <input type="text" name="request_number" id="title" class="form-control" readonly placeholder="{{ __('request_id') }}" >
                                <input type="hidden" name="request_id" id="request_id">
                                <input type="hidden" name="package_id" id="package_id">
                            </div>
                            <div class="col-md-6 form-group"><br><br>
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="manual_trip" class="form-input-styled required checkeds" data-fouc value="YES">
                                        Rental
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row packeges"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn1" onclick="Javascript: return categoryChange()" class="btn bg-primary">{{ __('save-changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- /horizontal form modal -->

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
        });
    }

    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                console.log(data);
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    $(document).on('click','.categoryChange',function(){
        var id = $(this).attr('id');
        var values = $(this).attr('data-value');
        $("#request_id").val(id);
        $("#title").val(values);
        $('#roleModel').modal('show');
    })

    $(document).on('click','.checkeds',function(){
        var id = $("#request_id").val();
        if($('input[name=manual_trip]').is(':checked')){
            $.ajax({
                url: "{{ url('request-views')}}/"+id,
                type: "GET",
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    var text = '';
                    $.each(data.packages, function( index, value ) {
                        text += "<div class='col-md-3'><div class='card card-body cardpackage' id='"+value.id+"'><h3>"+value.get_package.name+" ("+value.get_package.get_country.currency_symbol+" "+value.price+")</h3><span>"+value.get_package.hours+" Hr / "+value.get_package.km+" Km</span> </div></div>";   
                    });
                    $(".packeges").html(text);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
        else{
            $(".packeges").html('');
        }
    });

    $(document).on('click',".cardpackage",function(){
        $(".cardpackage").removeClass('bg-success');
        $(this).addClass('bg-success');
        $("#package_id").val($(this).attr('id'));
    })

    function categoryChange(){
        $.ajax({
            url: "{{ route('requestCategoryChange')}}",
            type: "POST",
            data: $("#roleForm").serialize(),
            dataType: 'json',
            success: function (data) {
                console.log(data);
                swal({
                    title: "Success",
                    text: data.message,
                    icon: "success",
                }).then((value) => {        
                    // $('#roleModel').modal('hide');
                    // $("#request_id").val('');
                    // $("#title").val('');
                    // $(".packeges").html('');
                    // $('input[name=manual_trip]').prop('checked', false);
                    location.reload();
                });
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

</script>

@endsection
