@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>

.select2-container{
    width: 100% !important;
}
.select2-selection{
    max-height: 0;
    overflow: hidden;
    overflow-y: scroll;
}
label .select2-container--default {
    display: none;
}

</style>

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('zone-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('add-zone'))
                        <a href="{{ route('addzone') }}" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('zone') }}</th>
                    <th>{{ __('zone_level') }}</th>
                    <th>{{ __('country') }}</th>
                    <th>{{ __('non_service_zone') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($zone as $key => $value)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $value->zone_name}}</td>
                        <td> 
                        @if($value->zone_level == "PRIMARY") 
                          <span class="badge badge-info">{{ __('Primary') }}</span> 
                        @else 
                          <span class="badge badge-secondary">{{ __('Secondary') }}</span> 
                        @endif
                        </td>
                        <td>{{ $value->getCountry ? $value->getCountry->name : ''}}</td>
                         <td> 
                        @if($value->non_service_zone == "Yes") 
                          <span class="badge badge-warning">{{ __('Yes') }}</span> 
                        @else 
                          <span class="badge badge-info bg-info-700" >{{ __('No') }}</span> 
                        @endif</td>
                        <td>
                            @if($value->status == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td>
                        <td>
                          <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                                        
                                    @if(auth()->user()->can('edit-zone'))
                                    <a href="{{ route('editZone',$value->slug) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit Zone</a>
                                @endif
                                @if(auth()->user()->can('zone-surge-price'))
                                    <a href="#" onclick="Javascript: return editAction(`{{$value->slug}}`,`{{ route('getZoneSrugePrice',$value->slug) }}`)" class="dropdown-item"><i class="icon-cash4"></i> Surge Price</a>
                                @endif
                                @if(auth()->user()->can('view-map-zone'))
                                    <a href="{{ route('viewMapZone',$value->slug) }}" class="dropdown-item"><i class="icon-map4"></i> View Map</a>
                                @endif
                                @if(auth()->user()->can('active-zone'))
                                    @if($value->status == 1)
                                        <a href="#" onclick="Javascript: return activeAction(`{{ route('activeZone',$value->slug) }}`)" class="dropdown-item"><i class="icon-user-block"></i>Block Zone</a>
                                    @else
                                        <a href="#"  onclick="Javascript: return activeAction(`{{ route('activeZone',$value->slug) }}`)" class="dropdown-item"><i class="icon-user-check"></i>Unblock Zone</a>
                                    @endif
                                @endif
                                @if(auth()->user()->can('delete-zone'))
                                    <div class="dropdown-divider"></div>
                                    <a href="#" onclick="Javascript: return deleteAction('$value->slug', `{{ route('deleteZone',$value->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete Zone</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

   <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent"></span>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('surge_price') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="surge_price" id="surge_price" value="" placeholder="{{ __('surge_price') }}" class="form-control" >
                                <input type="hidden" name="zone_id" id="zone_id">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('surge_distance_price') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="surge_distance_price" id="surge_distance_price" value="" placeholder="{{ __('surge_distance_price') }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('start_time') }}</label>
                            <div class="col-sm-9">
                                <input type="time" name="start_time" id="start_time" value="" placeholder="{{ __('start_time') }}" class="form-control" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('end_time') }}</label>
                            <div class="col-sm-9">
                                <input type="time" name="end_time" id="end_time" value="" placeholder="{{ __('end_time') }}" class="form-control" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('available_days') }}</label>
                            <div class="col-sm-9">
                                <select name="available_days[]" id="available_days" multiple="multiple" class="form-control" >
                                    <option value="Sunday">Sunday</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday" >Saturday</option>
                                </select>
                            </div>
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

<script>
    var message = "{{session()->get('message')}}";
    var status = "{{session()->get('status')}}";

    if(message && status == true){
        swal({
            title: "{{ __('success') }}",
            text: message,
            icon: "success",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

    if(message && status == false){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

    function editAction(value,actionUrl){
        $('#roleForm').trigger("reset");
        $('#zone_id').val(value);
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-zone-surge-price') }}");
                $('#errorbox').hide();
                $('#saveBtn').show();
                $('#roleModel').modal('show');
                $('#available_days').val(data.datas.available_days);
                $('#end_time').val(data.datas.end_time);
                $('#start_time').val(data.datas.start_time);
                $('#surge_price').val(data.datas.surge_price);
                $('#surge_distance_price').val(data.datas.surge_distance_price);
                $('#available_days').select2();
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        $('#errorbox').hide();
        $.ajax({
            data: $('#roleForm').serialize(),
            url: "{{ route('getZoneSrugePriceSave') }}",
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
                     $("#reloadDiv").load("{{ route('zone') }}");
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.errors);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    });
</script>
<script>
$(document).ready(function() {
    $('#available_days').select2();
});
</script>


@endsection
