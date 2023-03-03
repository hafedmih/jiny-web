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
            <h5 class="card-title">{{ __('manage-complaint') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-complaints'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Complaint list</h6>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-top nav-tabs-bottom nav-justified">
                <li class="nav-item"><a href="#top-justified-tab1" class="nav-link active" data-toggle="tab">{{ __('English') }}</a></li>
                <li class="nav-item"><a href="#top-justified-tab2" class="nav-link" data-toggle="tab">{{ __('tamil') }}</a></li>
            </ul>
            
            <div class="tab-content">
                <div class="tab-pane fade show active " id="top-justified-tab1"> 
                    <table class="table datatable-button-print-columns1 absolute" id="roletable">
                    <thead>
                        <tr>
                            <th>{{ __('sl') }}</th>
                            <th>{{ __('title') }}</th>
                            <th>{{ __('type') }}</th>
                            <th>{{ __('complaint_type') }}</th>
                            <th>{{ __('category') }}</th>
                            <th>{{ __('status') }}</th>
                            <th>{{ __('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(is_array($ComplaintList) || is_object($ComplaintList))
                        @php($key = 0)
                            @foreach( $sortedArr = collect($ComplaintList['en'])->sortByDesc('id')->all() as  $complaint)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $complaint['title'] }}</td>
                                    <td>{!! $complaint['type'] !!}</td> 
                                    <td>@if($complaint['complaint_type'] == 1)
                                            <span class="badge badge-success">{{ __('normal_complaints') }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ __('trip_complaints') }}</span>
                                        @endif
                                    </td> 
                                    <td>@if($complaint['category'] == 1)
                                            <span class="badge badge-primary">{{ __('complaints') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('suggestion') }}</span>
                                        @endif
                                    </td> 
                                    <td>@if($complaint['status'] == 1)
                                            <span class="badge badge-success">{{ __('active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('inactive') }}</span>
                                        @endif
                                    </td> 
                                
                                    <td>    
                                        <a class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7" ></i></a>
                                        <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: relative; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                            @if(auth()->user()->can('edit-complaints'))
                                            <a href="#" onclick="Javascript: return editAction(`{{ route('complaintsEdit',$complaint['slug']) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                            @endif
                                            @if(auth()->user()->can('delete-complaints'))
                                            <a href="#" onclick="Javascript: return deleteAction('$complaint->slug', `{{ route('complaintsDelete', $complaint['slug']) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                            @endif
                                            @if(auth()->user()->can('status-change-complaints'))
                                            <a href="#" onclick="Javascript: return activeAction( `{{ route('complaintsActive', $complaint['slug']) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
                                            @endif
                                        
                                        </div>        
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="top-justified-tab2">
                    <table class="table datatable-button-print-columns1" id="roletable">
                    <thead>
                        <tr>
                            <th>{{ __('sl') }}</th>
                            <th>{{ __('title') }}</th>
                            <th>{{ __('type') }}</th>
                            <th>{{ __('complaint_type') }}</th>
                            <th>{{ __('category') }}</th>
                            <th>{{ __('status') }}</th>
                            <th>{{ __('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(is_array($ComplaintList) || is_object($ComplaintList))
                        @php($key = 0)
                            @foreach($sortedArr = collect($ComplaintList['ta'])->sortByDesc('id')->all() as $complaint)
                                <tr>
                                    <td>{{ ++$key }}</td> 
                                    <td>{{ $complaint['title'] }}</td>
                                    <td>{!! $complaint['type'] !!}</td> 
                                    <td>@if($complaint['complaint_type'] == 1)
                                            <span class="badge badge-success">{{ __('normal_complaints') }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ __('trip_complaints') }}</span>
                                        @endif
                                    </td> 
                                    <td>@if($complaint['category'] == 1)
                                            <span class="badge badge-primary">{{ __('complaints') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('suggestion') }}</span>
                                        @endif
                                    </td> 
                                    <td>@if($complaint['status'] == 1)
                                            <span class="badge badge-success">{{ __('active') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('inactive') }}</span>
                                        @endif
                                    </td> 
                                    <td>    
                                        <a class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7" ></i></a>
                                        <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: relative; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                            @if(auth()->user()->can('edit-complaints'))
                                            <a href="#" onclick="Javascript: return editAction(`{{ route('complaintsEdit',$complaint['slug']) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                            @endif
                                            @if(auth()->user()->can('delete-complaints'))
                                            <a href="#" onclick="Javascript: return deleteAction('$complaint->slug', `{{ route('complaintsDelete', $complaint['slug']) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                            @endif
                                            @if(auth()->user()->can('status-change-complaints'))
                                            <a href="#" onclick="Javascript: return activeAction( `{{ route('complaintsActive', $complaint['slug']) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
                                            @endif
                                        
                                        </div>        
                                    </td>
                                </tr>
                            @endforeach
                        @endif
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
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('title') }}</label>
                            <div class="col-sm-9">
                                <textarea placeholder="Title" id="title" class="form-control" name="title"></textarea>
                                <input type="hidden" name="complaint_id" id="complaint_id">
                            </div>
                        </div>
                        <div class="form-group row required">
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
                            <label class="col-form-label col-sm-3">{{ __('complaint_type') }}</label>
                            <div class="col-sm-9">
                                <select id="complaint_type" class="form-control" name="complaint_type">
			                        <option value="">Select Complaint Type</option>
			                        <option value="1">{{ __('normal_complaints') }}</option>
			                        <option value="2">{{ __('trip_complaints') }}</option>
			                    </select>
                            </div>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('type') }}</label>
                            <div class="col-sm-9">
                                <select id="type" class="form-control" name="type">
			                        <option value="">Select Type</option>
			                        <option value="user">{{ __('user') }}</option>
			                        <option value="driver">{{ __('driver') }}</option>
			                    </select>
                            </div>
                        </div>
                        <div class="form-group row  required">
                            <label class="col-form-label col-sm-3">{{ __('language') }}</label>
                            <div class="col-sm-9">
                            <select id="language" class="form-control" name="language">
                                <option value="">{{ __('Select language') }}</option>
                                @if($languages->count() > 0)
                                    @foreach($languages as $language)
                                    @if($language->status == 1)
                                    <option value="{{ $language->code }}">{{ $language->name }}</option>
                                    @endif
                                    @endforeach
                                @endif
			                    </select>
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

<script type="text/javascript">
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-complaints') }}");
                $('#errorbox').hide();
                 $('#saveBtn').html("Edit Complaint");
                $('#saveBtn').val("edit_complaint");
                $('#roleModel').modal('show');
                $('#complaint_id').val(data.key);
                $('#title').val(data.complaint.title);
                $('#category').val(data.complaint.category);
                $('#type').val(data.complaint.type);
                $('#complaint_type').val(data.complaint.complaint_type);
                $('#language').val(data.complaint.language)
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
                                // $("#reloadDiv").load("{{ route('complaints') }}");
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
                            
                                // $("#reloadDiv").load("{{ route('complaints') }}");
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
        }
    });



  });
</script>

@endsection
