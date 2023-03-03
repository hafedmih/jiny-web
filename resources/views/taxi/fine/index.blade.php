@extends('layouts.app')

@section('content')


<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-fine') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('driver_name') }}</th>
                    <th>{{__('fine_amount') }}</th>
                    <th>{{ __('description') }}</th>
                    <th>{{ __('date') }}</th>
                    <!-- <th>{{ __('action') }}</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($fine_list as $key => $finemanage)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $finemanage->userDetail ? $finemanage->userDetail->firstname.' '.$finemanage->userDetail->lastname : '' !!}</td>
                        <td>{!! $finemanage->fine_amount!!}</td>
                        <td>{!! $finemanage->description!!}</td>
                        <td>{!! $finemanage->created_at!!}</td>
                        <!-- <td>
                        <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-type'))
                                <a href="#" onclick="Javascript: return editAction(`{{ route('fineUpdate',$users->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                @endif
                                @if(auth()->user()->can('delete-type'))
                                <a href="#" onclick="Javascript: return deleteAction('$users->slug', `{{ route('vehicleDelete',$users->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                @endif
                                
                            </div>
                        </td> -->
  
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
                <form id="roleForm" name="roleForm" class="form-horizontal" enctype="multipart/form-data">
                              @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent"></span>
                        </div>
                        <div class="form-group row ">
                            <label class="col-form-label col-sm-3">{{ __('driver_name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="driver_name" id="driver_name" class="form-control"  placeholder="Vehicle Name " >
                                
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('fine_amount') }}</label>
                            <div class="col-sm-9">
                                <input type="number" id="fine_amount" name="fine_amount" placeholder="fine_amount"  class="form-control" id="fine_amount" >
                                <input type="hidden" name="user_id" id="user_id" value="{{$users->id}}" />
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('description') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="description" id="description" class="form-control"  placeholder="description" >
                            </div>
                        </div>
                        
                        <div class="form-group row required">
                            <label class="col-form-label col-lg-3">{{ __('date') }}</label>
                            <div class="col-lg-9">
                               <input type="date" name="date" id="date" class="form-control"  placeholder="date" >
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
                $('#modelHeading').html("{{ __('edit-fine') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit-fine");
                $('#saveBtn').show();
                $('#roleModel').modal('show');
                $('#user_id').val(data.fine.user_id);
                $('#driver_name').val(data.fine.driver_name);
                $('#fine_amount').val(data.fine.fine_amount);
                $('#description').val(data.fine.description);
                $('#date').val(data.fine.date);
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
            // $('#user_id').val('');
            $('#roleForm').trigger("reset");
            $('#modelHeading').html("{{ __('create-fine') }}");
            $('#roleModel').modal('show');
            $('#saveBtn').val("add_fine");
            $('#errorbox').hide();
            // $("#view_image").hide();
            
        });


        $('#saveBtn').click(function (e) {
            e.preventDefault();
            var formData = new FormData();
            
            // formData.append('image',$('#image').prop('files')[0]);
            formData.append('driver_name',$('#driver_name').val());
            formData.append('fine_amount',$('#fine_amount').val());
            formData.append('description',$('#description').val());
            formData.append('user_id',$('#user_id').val());
            formData.append('date',$('#date').val());
            $(this).html("{{ __('sending') }}");
            var btnVal = $(this).val();
            
            $('#errorbox').hide();

            if(btnVal == 'edit-fine'){ 
                $.ajax({
                    data: formData,
                    url: "{{ route('fineUpdate') }}",
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
                    url: "{{route('driverfineSave')}}",
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
