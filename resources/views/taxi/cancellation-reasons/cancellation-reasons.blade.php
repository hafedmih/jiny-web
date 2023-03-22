@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('cancellation-reasons') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-category'))
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
                    <th>{{ __('reason_type') }}</th>
                    <th>{{ __('cancellation_reason') }}</th>
                    <th>{{ __('trip_status') }}</th>
                    <th>{{ __('pay_status') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cancellation as $key => $cancellations)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $cancellations->user_type!!}</td>
                        <td>{!! $cancellations->reason!!}</td>
                        <td>{!! $cancellations->trip_status!!}</td>
                        
                        <td>@if($cancellations->pay_status == 1)
                                <span class="badge badge-primary">{{ __('free') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('pay') }}</span>
                            @endif
                        </td>  
                        <td>@if($cancellations->active == 1)
                                <span class="badge badge-primary">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td>  
                        <td>    
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-category'))
                                <a href="#" onclick="Javascript: return editAction(`{{ route('cancelReasonEdit',$cancellations->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                @endif
                                @if(auth()->user()->can('delete-category'))
                                <a href="#" onclick="Javascript: return deleteAction('$cancellations->slug', `{{ route('cancelReasonDelete',$cancellations->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                @endif
                                @if(auth()->user()->can('active-category'))
                                <a href="#" onclick="Javascript: return activeAction(`{{ route('cancelReasonChangeStatus',$cancellations->slug) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
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
                        <div class="form-group row ">
                            <label class="col-form-label col-sm-3">{{ __('reason_type') }}</label>
                            <div class="col-sm-9">
                                <select name="reason_type" id="reason_type" class="form-control">
                                    <option value="">Select Category</option>
                                    <option value="user">{{ __('user') }}</option>
                                    <option value="driver">{{ __('driver') }}</option>
                                    <option value="both">{{ __('both') }}</option>
                                </select>
                                <input type="hidden" name="reason_id" id="reason_id">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('cancellation_reason') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="cancellation_reason" class="form-control" name="cancellation_reason">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('trip_status') }}</label>
                            <div class="col-sm-9">
                                <select name="trip_status" id="trip_status" class="form-control">
                                    <option value="">{{ __('trip_status') }}</option>
                                    <option value="{{ __('before_accept') }}">{{ __('before_accept') }}</option>
                                    <option value="{{ __('before_arrive') }}">{{ __('before_arrive') }}</option>
                                    <option value="{{ __('after_arrived') }}">{{ __('after_arrived') }}</option>
                                </select>
                                <input type="hidden" name="reason_id" id="reason_id">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('pay_status') }}</label>
                            <div class="col-sm-9">
                                <select name="pay_status" id="pay_status" class="form-control">
                                    <option value="">{{ __('pay_status') }}</option>
                                    <option value="1">{{ __('free') }}</option>
                                    <option value="2">{{ __('pay') }}</option>
                                </select>
                                <input type="hidden" name="reason_id" id="reason_id">
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
    $("#view_image").hide();
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-cancellation-reason') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit_reason");
                $('#saveBtn').show();
                $('#roleModel').modal('show');
                $('#reason_id').val(data.cancellation.slug);
                $('#reason_type').val(data.cancellation.user_type);
                $('#cancellation_reason').val(data.cancellation.reason);
                $('#trip_status').val(data.cancellation.trip_status);
                $('#pay_status').val(data.cancellation.pay_status);
                $('#view_image').show();
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
        $('#category_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-cancel-reason') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_reason");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        // formData.append('category_image',$('#category_image').prop('files')[0]);
        formData.append('cancellation_reason',$('#cancellation_reason').val());
        formData.append('reason_type',$('#reason_type').val());
        formData.append('reason_id',$('#reason_id').val());
        formData.append('trip_status',$('#trip_status').val());
        formData.append('pay_status',$('#pay_status').val());
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_reason'){
            $.ajax({
                data: formData,
                url: "{{ route('cancelReasonUpdate') }}",
                type: "POST",
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
                                // $("#reloadDiv").load("{{ route('cancellationReason') }}");
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
                url: "{{ route('cancelReasonSave') }}",
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
                            
                                // $("#reloadDiv").load("{{ route('cancellationReason') }}");
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
