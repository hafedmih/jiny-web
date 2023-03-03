@extends('layouts.app')

@section('content')


<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('push-notification') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-push-notification'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Push list</h6>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            
            <div class="tab-content">
                <div class="tab-pane fade show active" id="top-justified-tab1"> 
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                            <th>{{ __('sl') }}</th>
                            <th>{{ __('key') }}</th>
                            <th>{{ __('title') }}</th>
                            <th>{{ __('sub-title') }}</th>
                            <th>{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pushmasterlist as $key=> $value)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $value->key_value }}</td>
                                    <td>{{ $value->title }}</td>
                                    <td>{{ $value->description }}</td>
                                    <td>   
                                        <a class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7" ></i></a>
                                        <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: relative; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                            @if(auth()->user()->can('edit-push'))
                                            <a href="#" onclick="Javascript: return editAction(`{{ route('push-transaltion-edit',$value->id) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                            @endif
                                             @if(auth()->user()->can('delete-push'))
                                            <a href="#" onclick="Javascript: return deleteAction('$value->id', `{{ route('push-transaltion-delete', $value->id) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                            @endif
                                           
                                        </div>
                                                    
                                    </td> 
                                </tr>
                            @endforeach   
                        <tbody>
                    </table>
                </div>
               
            </div>
        </div>


    <!-- Horizontal form modal -->
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
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent"></span>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('key') }}</label>
                            <div class="col-sm-9">
                                <select name="key_value" class="form-control" id="key_value">
                                    <option value="trip-created">Trip Created</option>
                                    <option value="trip-started">Trip Started</option>
                                    <option value="trip-accept">Trip Accept</option>
                                    <option value="trip-cancel">Trip Cancel</option>
                                    <option value="trip-end">Trip End</option>

                                    <option value="trip-driver-cancel">Trip Cancelled Driver</option>
                                    <option value="no-driver">No Driver Found</option>
                                    <option value="driver-arrived">Driver Arrived</option>
                                    <option value="driver-blocked">Driver Blocked</option>
                                    <option value="driver-unblocked">Driver Unblocked</option>

                                    <option value="user-blocked">User Blocked</option>
                                    <option value="user-unblocked">User UnBlocked</option>

                                    <option value="location-changed">Location Changed</option>
                                    <option value="payment-done">Payment Done</option>
                                    <option value="user-change-payment">Change Payment</option>

                                    


                                </select>
                            </div>
                        </div>

                        <div class="form-group row hidding">
                            <label class="col-form-label col-sm-3">{{ __('language') }}</label>
                            <div class="col-sm-9">
                                 <select id="language" class="form-control" name="language">
                                    <option value="">{{ __('Select language') }}</option>
                                    @if($languages->count() > 0)
                                        @foreach($languages as $language)
                                        @if($language->status == 1)
                                        <option value="{{ $language->id }}">{{ $language->name }}</option>
                                        @endif
                                        @endforeach
                                    @endif
			                    </select>
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('title') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Title" id="title" class="form-control" name="title">
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('sub-title') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="SubTitle" id="description" class="form-control" name="description">
                            </div>
                        </div>

                        

                        <div hidden class="form-group row required ">
                            <div class="col-sm-9">
                                <input type="text" value="1" id="status" class="form-control" name="status">
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
<!-- /horizontal form modal -->

<!-- <script type="text/javascript">
      function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $('#modelHeading').html("{{ __('edit-push') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit-push");
                $('#saveBtn').show();
                $('#roleModel').modal('show');
                $('#key_value').val(data.push.key_value);
                $('#title').val(data.push.title);
                $('#description').val(data.push.description);
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
        $('#push_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-push-notification') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add-push-notification");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
       
        $.ajax({
            data: $('#roleForm').serialize(),
            url: "{{ route('push-transaltion-save') }}",
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
                        
                            // $("#reloadDiv").load("{{ route('sos-management') }}");
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
               //console.log(data);
                $('#modelHeading').html("{{ __('edit-push') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit_push");
                $('#roleModel').modal('show');
                $('#key_value').val(data.push.key_value);
                $('#title').val(data.push.title);
                $('#description').val(data.push.description);
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
        $('#modelHeading').html("{{ __('create-new-push') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_push");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_push'){
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('push-transaltion-update') }}",
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
                url: "{{ route('push-transaltion-save') }}",
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
