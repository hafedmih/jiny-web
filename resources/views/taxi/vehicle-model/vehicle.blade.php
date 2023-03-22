@extends('layouts.app')

@section('content')
<style>
    .form-group.required .col-form-label:after {
                content:" *";
                color: red;
                weight:100px;
            }

</style>

<div class="content">
     
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-vehicle-model') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-model'))
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
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('vehicle_id') }}</th>
                    <th>{{ __('hint_vehicle_model') }}</th>
                    <!-- <th>{{ __('images') }}</th> -->
                    <th>{{ __('description') }}</th>
                    <th>{{__('status')}}</th>
                    <th>{{ __('action') }}</th>
                    
                </tr>
            </thead>
            <tbody>

                @foreach($vehicleList as $key => $vehicle)
                    <tr>
                        <td>{{ ++$key }}</td>                        
                        <td>{!! $vehicle->getVehicle ? $vehicle->getVehicle->vehicle_name : '' !!}</td>
                        <td>{!! $vehicle->model_name!!}</td>
                        <!-- <td>
                        <img src="{{ $vehicle->image}}" height="40px" width="auto" alt="" />
                        </td>      -->
                        <td>{!! $vehicle->description!!}</td>
                        <td>@if($vehicle->status == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td> 
                        
                        <td>    
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-model'))
                                <a href="#" onclick="Javascript: return editAction(`{{ route('vehiclemodelEdit',$vehicle->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                @endif
                                @if(auth()->user()->can('delete-model'))
                                <a href="#" onclick="Javascript: return deleteAction('$vehicle->slug', `{{ route('vehicleModelDelete',$vehicle->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                @endif
                                @if(auth()->user()->can('active-model'))
                                <a href="#" onclick="Javascript: return activeAction(`{{ route('vehicleModelStatusChange',$vehicle->slug) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
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
                    <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" class="form-horizontal" enctype="multipart/form-data">
                              @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent"></span>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('vehicle_model') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="model_name" id="model_name" class="form-control"  placeholder="Vehicle Model " />
                                <input type="hidden" name="vehiclemodel_id" id="vehiclemodel_id" /> 
                            </div>
                        </div>


                        <div class="form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('vehicle_List') }}</label>
                            <div class="col-lg-9">
                                <select class="form-control" name="vehicle_id" id="vehicle_id">
			                        <option value="">Select Vehicle</option>
			                        @foreach($vehicles as $value)
			                            <option value="{{$value->slug}}">{{$value->vehicle_name}} </option>
                                    @endforeach
		                        </select>
                            </div>
                        </div>
                       

                        <!-- <div class="form-group row ">
                            <label class="col-form-label col-sm-3">{{ __('images') }}</label>
                            <div class="col-sm-9">
                                <input type="file" placeholder="vehicle image"  class="form-control" id="image" />
                                <img src="" id="view_image" width="100px" >
                            </div>
                        </div> -->

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('description') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="description" id="description" class="form-control"  placeholder="description" >
                            </div>
                        </div>
                        
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
</div>
<!-- /horizontal form modal -->


<script type="text/javascript">
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                 console.log(data);
                $('#modelHeading').html("{{ __('edit-vehicle') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit-vehicle");
                $('#saveBtn').show();
                $('#roleModel').modal('show');
                // $('#category_id').val(data.vehicle.get_category ? data.vehicle.get_category.slug : '');
                $('#model_name').val(data.vehicle.model_name);
                // $('#image').attr('src',data.vehicle.image);
                $('#vehiclemodel_id').val(data.vehicle.slug);
                $('#vehicle_id').val(data.vehicle.get_vehicle ? data.vehicle.get_vehicle.slug : '');
                $('#description').val(data.vehicle.description);
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
        $('#vehiclemodel_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-vehicle') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_vehicle");
        $('#errorbox').hide();
        $("#view_image").hide();
    });

    $('#image').change(function(){
        $("#view_image").show();
          let reader = new FileReader();
          reader.onload = (e) => { 
            $('#image_value').val(e.target.result); 
            $("#view_image").attr('src',e.target.result);
          }
          reader.readAsDataURL(this.files[0]); 
      });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        // formData.append('image',$('#image').prop('files')[0]);
        formData.append('model_name',$('#model_name').val());
        formData.append('description',$('#description').val());
         formData.append('vehicle_id',$('#vehicle_id').val());
         formData.append('vehiclemodel_id',$('#vehiclemodel_id').val());

        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
        if(btnVal == 'edit-vehicle'){
            $.ajax({
                data: formData,
                url: "{{ route('vehiclemodelUpdate') }}",
                type: "POST",
                // enctype: 'multipart/form-data',
                dataType: 'json',
                contentType : false,
                processData: false,
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-updated') }}",
                            text: "{{ __('data-updated-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                                // $("#reloadDiv").load("{{ route('vehicle') }}");
                                location.reload();
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
        }else{
            $.ajax({
                data: formData,
                url: "{{route('vehiclemodelSave')}}",
                type: "POST",
                dataType: 'json',
                contentType : false,
                processData: false,
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-added') }}",
                            text: "{{ __('data-added-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                            
                                // $("#reloadDiv").load("{{ route('vehicle') }}");
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
        }
    });



  });

  
</script>
@endsection
