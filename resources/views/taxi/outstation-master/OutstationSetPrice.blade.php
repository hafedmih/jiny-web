@extends('layouts.app')

@section('content')


<div class="content">
    <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('outstation-set-pricing') }}</h5>
                <div class="header-elements">
                    <!-- <div class="list-icons">
                        @if(auth()->user()->can('new-outstation'))
                            <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('set-price') }}</button>
                        @endif
                    </div> -->
                </div>
            </div>
    </div>

    <!-- <div class="card" id="tableDiv">
        <form id="roleForm" name="roleForm" class="form-horizontal">
        @csrf
        <div class="card-body">
        
            <table class="table" id="">
                <thead>
                    <tr>
                        <th>{{ __('txt_veh_type') }}</th>
                        <th>{{ __('distance_price') }} (1km)</th>
                        <th>{{ __('distance_price_two_way') }}(1km)</th>
                        <th>{{ __('admin_commission_type') }}</th>
                        <th>{{ __('admin_commission') }}</th>
                        <th>{{ __('driver_price') }}</th>
                        <th>{{ __('grace_time') }}</th>
                        <th>{{ __('hill_station_price') }}</th>
                        <th>{{ __('waiting_charge') }}</th>
                        <th>{{ __('base_fare') }}</th>
                        <th>{{ __('minimum_km') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($OutstationMaster as $key => $list)
                        <tr>
                            <td><b>{!! $list->vehicle_name !!}</b><input type="hidden" name="type_id[]" value="{{$list->id}}" /></td>
                            <td><input type="text" name="distance_price[]" class="form-control" value="{{$list->getOutstationPrice ? $list->getOutstationPrice->distance_price : ''}}" /></td> 
                            <td><input type="text" name="distance_price_two_way[]" class="form-control" value="{{$list->getOutstationPrice ? $list->getOutstationPrice->distance_price_two_way : ''}}" /></td>
                            <td><select class="form-control" name="admin_commission_type[]" id="admin_commission_type" required>
                                <option value="1" {{(old('admin_commission_type',$list->getOutstationPrice ? $list->getOutstationPrice->admin_commission_type : '') == 1 )?'selected':''}}>Percentage</option>
                                <option value="0" {{(old('admin_commission_type',$list->getOutstationPrice ? $list->getOutstationPrice->admin_commission_type : '') == 0 )?'selected':''}}>Fixed </option>
                            </select></td> 
                            <td><input type="text" name="admin_commission[]" class="form-control" value=" {{$list->getOutstationPrice ? $list->getOutstationPrice->admin_commission : ''}}" /></td> 
                            <td><input type="text" name="driver_price[]" class="form-control" value=" {{$list->getOutstationPrice ? $list->getOutstationPrice->driver_price : ''}}" /></td> 
                            <td><input type="text" name="grace_time[]" class="form-control" value="{{$list->getOutstationPrice ? $list->getOutstationPrice->grace_time : ''}}" /></td> 
                            <td><input type="text" name="hill_station_price[]" class="form-control" value="{{$list->getOutstationPrice ? $list->getOutstationPrice->hill_station_price : ''}}" /></td> 
                            <td><input type="text" name="waiting_charge[]" class="form-control" value=" {{$list->getOutstationPrice ? $list->getOutstationPrice->waiting_charge : ''}}" /></td> 
                            <td><input type="text" name="base_fare[]" class="form-control" value=" {{$list->getOutstationPrice ? $list->getOutstationPrice->base_fare : ''}}" /></td>
                            <td><input type="text" name="minimum_km[]" class="form-control" value=" {{$list->getOutstationPrice ? $list->getOutstationPrice->minimum_km : ''}}" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>  
        </div>  
        <div class="card-footer text-right">
        @if(auth()->user()->can('setprice-outstation'))
            <button type="button" id="saveBtn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('set-price') }}</button>
        @endif    
        </div>
        </form>
    </div> -->
    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('s.no') }}</th>
                    <th>{{ __('vehicle_name') }}</th>
                    <th>{{ __('distance_price') }}</th>
                    <th>{{ __('distance_price_two_way') }}</th>
                    
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($OutstationMaster as $key => $list)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td><b>{!! $list->vehicle_name !!}</b><input type="hidden" name="type_id[]" value="{{$list->id}}" /></td>
                        <td>{!! $list->getOutstationPrice ? $list->getOutstationPrice->distance_price : '' !!}</td>
                        <td>{!! $list->getOutstationPrice ? $list->getOutstationPrice->distance_price_two_way : '' !!}</td> 
                         
                        
                        <td>                               
                            <a class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7" ></i></a>
                                <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: relative; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                @if(auth()->user()->can('edit-outstation'))
                                    <a href="#" onclick="Javascript: return editAction(`{{ route('outstationSetPriceEdit',$list->id) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                    @endif
                                </div>          
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>    
    </div>
    
    <div id="roleModel" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form id="roleForm" name="roleForm" class="form-horizontal">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-danger" id="errorbox">
                                <button type="button" class="close"><span>×</span></button>
                                <span id="errorContent"></span>
                            </div>
                            <!-- <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('vehicle_name') }}</label>
                                <div class="col-sm-9"><input type="text" placeholder="{{ __('vehicle_name') }}" id="type_id" class="form-control" name="type_id">
                                    <input type="hidden" name="type_id" id="type_id">
                                </div>
                            </div> -->

                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('admin_commission_type') }}</label>
                                
                                <select class="form-control col-sm-9" name="admin_commission_type" id="admin_commission_type">
			                            
                                        <option value="1" {{(old('admin_commission_type',$list->getOutstationPrice ? $list->getOutstationPrice->admin_commission_type : '') == 1 )?'selected':''}}>Percentage</option>
                                        <option value="0" {{(old('admin_commission_type',$list->getOutstationPrice ? $list->getOutstationPrice->admin_commission_type : '') == 0 )?'selected':''}}>Fixed </option>
                                   
                                    
		                        </select>
                            
                            </div>
                            <div class="form-group row required">
                                
                                </div>
    
                                   <div class="form-group row required">
                                    <label class="col-form-label col-sm-3 ">{{ __('admin_commission') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" placeholder="{{ __('admin_commission') }}" id="admin_commission" class="form-control" name="admin_commission">
                                    </div>
                                </div>

                             <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('distance_price') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('distance_price') }}" id="distance_price" class="form-control" name="distance_price">
                                    <input type="hidden" name="type_id" id="type_id">
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('driver_price') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('driver_price') }}" id="driver_price" class="form-control" name="driver_price">
                                </div>
                            </div>


                             <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('distance_price_two_way') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('distance_price_two_way') }}" id="distance_price_two_way" class="form-control" name="distance_price_two_way">
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('driver_price_2_way') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('driver_price_2_way') }}" id="day_rent_two_way" class="form-control" name="day_rent_two_way">
                                </div>
                            </div>
                            


                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3">{{ __('grace_time') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('grace_time') }}" id="grace_time" class="form-control" name="grace_time">
                                </div>
                            </div>
                           

                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{{ __('waiting_charge') }}</label>
                                <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('waiting_charge') }}" id="waiting_charge" class="form-control" name="waiting_charge" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{{ __('hill_station_price') }}</label>
                                <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('hill_station_price') }}" id="hill_station_price" class="form-control" name="hill_station_price" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{{ __('base_fare') }}</label>
                                <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('base_fare') }}" id="base_fare" class="form-control" name="base_fare" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{{ __('minimum_km') }}</label>
                                <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('minimum_km') }}" id="minimum_km" class="form-control" name="minimum_km" >
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                            <button type="submit" id="saveBtn" class="btn bg-primary">{{ __('set-price') }}</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>


<!-- <script type="text/javascript">
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               //console.log(data);
                $('#modelHeading').html("{{ __('edit-subscription') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit_user");
                $('#roleModel').modal('show');
                $('#user_id').val(data.list.slug);
                $('#pick_up').val(data.list.pick_up);
                $('#drop').val(data.list.drop);
                $('#distance').val(data.list.distance);
                $('#price').val(data.list.price);
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });


    $('#add_new_btn').click(function () {
        
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_user");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        $('#errorbox').hide();
        $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('outstationSetPriceSave') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    swal({
                        title: "{{ __('data-added') }}",
                        text: "{{ __('data-added-successfully') }}",
                        icon: "success",
                    }).then((value) => {        
                        // $("#reloadDiv").load("{{ route('sublist') }}");
                        location.reload();
                    });                
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    $('#errorContent').html('');
                    $.each(err.errors, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtn').html('<i class="icon-plus3 mr-2"></i> {{ __("set-price") }}');
                }
        });
    });

  });
</script> -->
<script type="text/javascript">
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
               
                $('#modelHeading').html("{{ __('set-price') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit_user");
                $('#roleModel').modal('show');
                $('#type_id').val(data.type_id.type_id);
                $('#distance_price').val(data.Outstation.distance_price);
                $('#distance_price_two_way').val(data.Outstation.distance_price_two_way);
                $('#admin_commission_type').val(data.Outstation.admin_commission_type);
                $('#admin_commission').val(data.Outstation.admin_commission);
                $('#driver_price').val(data.Outstation.driver_price);
                $('#grace_time').val(data.Outstation.grace_time);
                $('#hill_station_price').val(data.Outstation.hill_station_price);
                $('#waiting_charge').val(data.Outstation.waiting_charge);
                $('#day_rent_two_way').val(data.Outstation.day_rent_two_way);
                $('#base_fare').val(data.Outstation.base_fare);
                $('#minimum_km').val(data.Outstation.minimum_km);
                
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });


    $('#add_new_btn').click(function () {
        
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-outstation') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_user");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_user'){
            // alert( $('#day_rent_two_way').val());
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('outstationSetPriceSave') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-updated') }}",
                            text: "{{ __('data-updated-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                                // $("#reloadDiv").load("{{ route('sublist') }}");
                                location.reload();
                            });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err);
                    $('#errorContent').html('');
                    $.each(err.errors, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtn').html("{{ __('save-changes') }}");
                }
            });
        }else{
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('outstationSetPriceSave') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-added') }}",
                            text: "{{ __('data-added-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                            
                                // $("#reloadDiv").load("{{ route('sublist') }}");
                                location.reload();
                            });
                        
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#errorbox').show();
                        var err = eval("(" + xhr.responseText + ")");
                        $('#errorContent').html('');
                        $.each(err.errors, function(key, value) {
                            $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                        });
                        $('#saveBtn').html("{{ __('save-changes') }}");
                    }
                });
        }
    });

  });
</script>
@endsection