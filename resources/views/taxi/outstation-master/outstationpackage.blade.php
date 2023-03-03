@extends('layouts.app')

@section('content')


<div class="content">
    <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('outstation-package-master') }}</h5>
                <div class="header-elements">
                    <div class="list-icons">
                        @if(auth()->user()->can('new-outstationpack'))
                            <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                        @endif
                    </div>
                </div>
            </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('s.no') }}</th>
                    <th>{{ __('base_price') }}</th>
                    <th>{{ __('driver_bata') }}</th>
                    <th>{{ __('price_per_km') }}</th>
                    <th>{{ __('hours') }}</th>
                    <th>{{ __('vehicle_type') }}</th>
                    <th>{{ __('package_name') }}</th>
                    <th>{{__('status')}}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outstationpackage as $key => $package)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $package->base_price ? $package->base_price : '' !!}</td>
                        <td>{!! $package->driver_bata ? $package->driver_bata : '' !!}</td> 
                        <td>{!! $package->price_per_km ? $package->price_per_km : '' !!}</td> 
                        <td>{!! $package->hours ? $package->hours : '' !!}</td> 
                        <td>{!! $package->getVehicletype ? $package->getVehicletype->vehicle_name : '' !!}</td>
                        <td>{!! $package->package_name ? $package->package_name : '' !!}</td>     
                        <td>
                            @if($package['status'] == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td>       
                                        
                        
                        <td>                               
                            <a class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7" ></i></a>
                                <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: relative; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                    @if(auth()->user()->can('edit-outstation'))
                                    <a href="#" onclick="Javascript: return editAction(`{{ route('outstationpackageedit',$package->id) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                   
                                    @endif
                                    @if(auth()->user()->can('delete-outstation'))
                                    <a href="#" onclick="Javascript: return deleteAction('$package->id', `{{ route('outstationpackagedelete',$package->id) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                    @endif
                                    @if(auth()->user()->can('status-change-outstation'))
                                    <a href="#" onclick="Javascript: return activeAction( `{{ route('outstationpackageactive',  $package->id) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
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
                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('base_price') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('base_price') }}" id="base_price" class="form-control" name="base_price">
                                    <input type="hidden" name="id" id="id">
                                </div>
                            </div>

                             <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('driver_bata') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('driver_bata') }}" id="driver_bata" class="form-control" name="driver_bata">
                                </div>
                            </div>

                             <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('price_per_km') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('price_per_km') }}" id="price_per_km" class="form-control" name="price_per_km">
                                </div>
                            </div>


                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3">{{ __('hours') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('hours') }}" id="hours" class="form-control" name="hours">
                                </div>
                            </div>

                               <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('package_name') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('package_name') }}" id="package_name" class="form-control" name="package_name">
                                </div>
                            </div>
                            <div class="form-group row">
                            <label class="col-form-label col-lg-3">{{ __('vehicle_type') }}</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="vehicle_type" id="vehicle_type">
			                        <option value="">Select type</option>

			                        @foreach($vehicle as $value)
                                    
			                            <option value="{{$value->slug}}">{{$value->vehicle_name}} </option>
                                    @endforeach
		                        </select>
                            </div>
                        </div>
                             
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                            <button type="submit" id="saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>

    <script type="text/javascript">
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
            //    console.log(data);
            // console.log(data.Outstation);
                $('#modelHeading').html("{{ __('edit-outstation') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit_user");
                $('#roleModel').modal('show');
                $('#id').val(data.Outstation.id);
                $('#base_price').val(data.Outstation.base_price);
                $('#driver_bata').val(data.Outstation.driver_bata);
                $('#price_per_km').val(data.Outstation.price_per_km);
                $('#hours').val(data.Outstation.hours);
                $('#package_name').val(data.Outstation.package_name);
                $('#vehicle_type').val(data.Outstation.get_vehicletype ? data.Outstation.get_vehicletype.slug : '');
                
                

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
        $('#modelHeading').html("{{ __('create-package') }}");
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
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('outstationpackageupdate') }}",
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
                url: "{{ route('outstationpackagesave') }}",
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