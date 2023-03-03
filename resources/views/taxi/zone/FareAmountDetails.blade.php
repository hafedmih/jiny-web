@extends('layouts.app')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">
<style>
    table.dataTable tbody td {
  word-break: break-word; white-space: normal;
}
</style>

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-request') }}</h5>
            
        </div>
    </div>

    <div class="card" id="tableDiv">
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-bottom">
			    <li class="nav-item"><a href="#justified-right-icon-tab1" class="nav-link active" data-toggle="tab"><i class="icon-racing"></i> {{ __('local')}}</a></li>
				<li class="nav-item"><a href="#justified-right-icon-tab2" class="nav-link" data-toggle="tab"><i class="icon-hour-glass2"></i> {{ __('rental')}}  </a></li> 
                <!-- <li class="nav-item"><a href="#justified-right-icon-tab3" class="nav-link" data-toggle="tab"><i class="icon-forward"></i> {{ __('outstation')}}</a></li> -->
			</ul>
		    <div class="tab-content">
				<div class="tab-pane fade show active" id="justified-right-icon-tab1">
                    @foreach($local as $key => $request)
                        <h3>{{$request->zone_name}}</h3>
                        <table class="table table-responsive" id="roletable">
                            <thead>
                                <tr>
                                    <th>{{ __('sl') }}</th>
                                    <th>{{ __('vehicle_type') }}</th>
                                    <th>{{ __('type') }}</th>
                                    <th>{{ __('base_price') }}</th>
                                    <th>{{ __('price_per_time') }}</th>
                                    <th>{{ __('base_distance') }}</th>
                                    <th>{{ __('price_per_distance') }}</th>
                                    <th>{{ __('free_waiting_time') }}</th>
                                    <th>{{ __('waiting_charge') }}</th>
                                    <th>{{ __('cancellation_fee') }}</th>
                                    <th>{{ __('booking_base_fare') }}</th>
                                    <th>{{ __('booking_base_per_kilometer') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($request->getZonePrice as $key => $request1)
                                    <tr>
                                        <td rowspan="2">{{ ++$key }}</td>
                                        <td rowspan="2">{{$request1->getType ? $request1->getType->vehicle_name : ''}}</td>
                                        <td>Ride Now</td>
                                        <td>{!! $request1->ridenow_base_price !!}</td>
                                        <td>{!! $request1->ridenow_price_per_time !!}</td>
                                        <td>{!! $request1->ridenow_base_distance !!}</td>
                                        <td>{!! $request1->ridenow_price_per_distance !!}</td>
                                        <td>{!! $request1->ridenow_free_waiting_time !!}</td>
                                        <td>{!! $request1->ridenow_waiting_charge !!}</td>
                                        <td>{!! $request1->ridenow_cancellation_fee !!}</td>
                                        <td>{!! $request1->ridenow_booking_base_fare !!}</td>
                                        <td>{!! $request1->ridenow_booking_base_per_kilometer !!}</td>
                                    </tr><tr>
                                        <td>Ride Later</td>
                                        <td>{!! $request1->ridelater_base_price !!}</td>
                                        <td>{!! $request1->ridelater_price_per_time !!}</td>
                                        <td>{!! $request1->ridelater_base_distance !!}</td>
                                        <td>{!! $request1->ridelater_price_per_distance !!}</td>
                                        <td>{!! $request1->ridelater_free_waiting_time !!}</td>
                                        <td>{!! $request1->ridelater_waiting_charge !!}</td>
                                        <td>{!! $request1->ridelater_cancellation_fee !!}</td>
                                        <td>{!! $request1->ridelater_booking_base_fare !!}</td>
                                        <td>{!! $request1->ridelater_booking_base_per_kilometer !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
				</div>
				<div class="tab-pane fade" id="justified-right-icon-tab2">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('package_name') }}</th>
                                <th>{{ __('km') }}</th>
                                <th>{{ __('hr') }}</th>
                                @foreach($types as $key => $request)
                                <th>{{ $request->vehicle_name }}</th>
                                @endforeach
                                <th>{{ __('base_fare') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rental as $key => $request)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        {!! $request->name !!}
                                    </td>
                                    <td>
                                            {!! $request->km !!}
                                    </td>
                                    <td>
                                        {!! $request->hours !!}
                                    </td>
                                    @foreach($types as $key => $request1)
                                        @php $i = 0; @endphp
                                        @foreach($request->getPackageItems as $key => $request2)
                                            @if($request1->id == $request2->type_id)<td>{{$request2->price}}</td> 
                                                @php $i++; @endphp
                                            @endif
                                        @endforeach
                                        @if($i == 0)
                                            <td></td>
                                        @endif
                                    @endforeach

                                    <td>{!! $request->driver_price !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
				</div>
                <div class="tab-pane fade" id="justified-right-icon-tab3">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('vehicle_type') }}</th>
                                <th>{{ __('distance_price') }}</th>
                                <th>{{ __('driver_bata') }}</th>
                                <th>{{ __('hill_station') }}</th>
                                <th>{{ __('waiting_charge') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outstation as $key => $request)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        {!! $request->getVehicle ? $request->getVehicle->vehicle_name : '' !!} 
                                    </td>
                                    <td>
                                        {!! $request->distance_price !!}
                                    </td>
                                    <td>
                                        {!! $request->driver_price !!}
                                    </td>
                                    <td>{!! $request->hill_station_price!!}</td>
                                    <td>{!! $request->waiting_charge !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
				</div>
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
