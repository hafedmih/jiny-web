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

    <div class="card" id="tableDiv">
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
    </div>
    
</div>


<script type="text/javascript">
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
</script>

@endsection