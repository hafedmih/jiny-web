@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('complaint-list') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-complaint'))
                        <!-- <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button> -->
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="tableDiv">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">{{ __('complaint-list') }}</h6>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item "><a href="#right-icon-tab1" class="nav-link active " data-toggle="tab"><i class="icon-file-text ml-2 text-success-800"></i><span class="text-success-400"> Normal Complaints </span></a></li>
                <li class="nav-item"><a href="#right-icon-tab2" class="nav-link" data-toggle="tab"><i class="icon-file-text ml-2 text-success-800"></i> <span class="text-success-800"> Request Complaints </span> </a></li>      
                <li class="nav-item "><a href="#right-icon-tab3" class="nav-link" data-toggle="tab"><i class="icon-file-text ml-2 text-danger-800"></i><span class="text-danger-400"> Normal Suggestion </span></a></li>
                <li class="nav-item"><a href="#right-icon-tab4" class="nav-link" data-toggle="tab"><i class="icon-file-text ml-2 text-danger-800"></i> <span class="text-danger-800"> Request Suggestion </span> </a></li>             
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="right-icon-tab1">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('title') }}</th>
                                <th>{{ __('answer') }}</th>
                                <th>{{ __('category') }}</th>
                                <th>{{ __('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_complaint as $key => $complaint)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{!! $complaint->userDetails ? $complaint->userDetails->firstname : '' !!} {!! $complaint->userDetails ? $complaint->userDetails->lastname : '' !!}</td>
                                    <td>{!! $complaint->complaintDetails ? $complaint->complaintDetails->title : '' !!}</td> 
                                    <td>{!! $complaint->answer !!}</td> 
                                    <td>@if($complaint->category == 1)
                                            <span class="badge badge-primary">{{ __('complaints') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('suggestion') }}</span>
                                        @endif
                                    </td> 
                                    <td>@if($complaint->status == 1)
                                            <span class="badge badge-success">{{ __('active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('inactive') }}</span>
                                        @endif
                                    </td> 
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="right-icon-tab2">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('title') }}</th>
                                <th>{{ __('answer') }}</th>
                                <th>{{ __('request_code') }}</th>
                                <th>{{ __('category') }}</th>
                                <th>{{ __('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_complaint_request as $key => $complaint)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{!! $complaint->userDetails ? $complaint->userDetails->firstname : '' !!} {!! $complaint->userDetails ? $complaint->userDetails->lastname : '' !!}</td>
                                    <td>{!! $complaint->complaintDetails ? $complaint->complaintDetails->title : '' !!}</td> 
                                    <td>{!! $complaint->answer !!}</td> 
                                    <td>{!! $complaint->requestDetails ? $complaint->requestDetails->request_number : '' !!}</td> 
                                    <td>@if($complaint->category == 1)
                                            <span class="badge badge-primary">{{ __('complaints') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('suggestion') }}</span>
                                        @endif
                                    </td> 
                                    <td>@if($complaint->status == 1)
                                            <span class="badge badge-success">{{ __('active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('inactive') }}</span>
                                        @endif
                                    </td> 
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="right-icon-tab3">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('title') }}</th>
                                <th>{{ __('answer') }}</th>
                                <th>{{ __('category') }}</th>
                                <th>{{ __('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_suggession as $key => $complaint)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{!! $complaint->userDetails ? $complaint->userDetails->firstname : '' !!} {!! $complaint->userDetails ? $complaint->userDetails->lastname : '' !!}</td>
                                    <td>{!! $complaint->complaintDetails ? $complaint->complaintDetails->title : '' !!}</td> 
                                    <td>{!! $complaint->answer !!}</td> 
                                    <td>@if($complaint->category == 1)
                                            <span class="badge badge-primary">{{ __('complaints') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('suggestion') }}</span>
                                        @endif
                                    </td> 
                                    <td>@if($complaint->status == 1)
                                            <span class="badge badge-success">{{ __('active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('inactive') }}</span>
                                        @endif
                                    </td> 
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="right-icon-tab4">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('title') }}</th>
                                <th>{{ __('answer') }}</th>
                                <th>{{ __('request_code') }}</th>
                                <th>{{ __('category') }}</th>
                                <th>{{ __('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user_suggession_request as $key => $complaint)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{!! $complaint->userDetails ? $complaint->userDetails->firstname : '' !!} {!! $complaint->userDetails ? $complaint->userDetails->lastname : '' !!}</td>
                                    <td>{!! $complaint->complaintDetails ? $complaint->complaintDetails->title : '' !!}</td> 
                                    <td>{!! $complaint->answer !!}</td> 
                                    <td>{!! $complaint->requestDetails ? $complaint->requestDetails->request_number : '' !!}</td> 
                                    <td>@if($complaint->category == 1)
                                            <span class="badge badge-primary">{{ __('complaints') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('suggestion') }}</span>
                                        @endif
                                    </td> 
                                    <td>@if($complaint->status == 1)
                                            <span class="badge badge-success">{{ __('active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('inactive') }}</span>
                                        @endif
                                    </td> 
                                </tr>
                            @endforeach
                        </tbody>
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
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('title') }}</label>
                            <div class="col-sm-9">
                                <textarea placeholder="Title" id="title" class="form-control" name="title"></textarea>
                                <input type="hidden" name="complaint_id" id="complaint_id">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('category') }}</label>
                            <div class="col-sm-9">
                                <select id="category" class="form-control" name="category">
			                        <option value="">Select Category</option>
			                        <option value="1">{{ __('complaints') }}</option>
			                        <option value="2">{{ __('suggestion') }}</option>
			                    </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('type') }}</label>
                            <div class="col-sm-9">
                                <select id="type" class="form-control" name="type">
			                        <option value="">Select Type</option>
			                        <option value="user">{{ __('user') }}</option>
			                        <option value="driver">{{ __('driver') }}</option>
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
<!-- /horizontal form modal -->

<script type="text/javascript">
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-complaint') }}");
                $('#errorbox').hide();
                // $('#saveBtn').html("Edit Complaint");
                $('#saveBtn').val("edit_complaint");
                $('#roleModel').modal('show');
                $('#complaint_id').val(data.complaint.slug);
                $('#title').val(data.complaint.title);
                $('#category').val(data.complaint.category);
                $('#type').val(data.complaint.type);
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
        $('#complaint_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-complaint') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_complaint");
        // $('#saveBtn').html("Save Complaint");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_complaint'){
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('complaintsUpdate') }}",
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
                                $("#reloadDiv").load("{{ route('complaints') }}");
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
                data: $('#roleForm').serialize(),
                url: "{{ route('complaintsSave') }}",
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
                            
                                $("#reloadDiv").load("{{ route('complaints') }}");
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
        }
    });



  });
</script>

@endsection
