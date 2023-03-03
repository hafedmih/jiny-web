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
            <h5 class="card-title">{{ __('roles-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-role'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i>{{ __('add-new') }}</button>
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
                    <th>{{ __('name') }}</th>
                    <th>{{ __('description') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rolesList as $key => $role)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $role->display_name !!}</td>
                        <td>{!! Str::limit($role->description,50) !!}</td> 
                        <td>    
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-role'))
                                <a href="#" onclick="Javascript: return editAction('$role->slug', `{{ route('roles.edit',$role->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                @endif
                                @if(auth()->user()->can('delete-role'))
                                <!-- <a href="#" onclick="destroy('$role->slug')" class="dropdown-item deleteProduct"><i class="icon-trash"></i> Delete</a> -->
                                <a href="#" onclick="Javascript: return deleteAction('$role->slug', `{{ route('roles.destroy',$role->slug) }}`)" id="delbutton" class="dropdown-item deleteProduct"><i class="icon-trash"></i> Delete</a>
                                @endif
                                @if(auth()->user()->can('assign-role-permission'))
                                <a  href="{{ url('/roles/'.$role->slug) }}" class="dropdown-item" ><i class="icon-user-tie"></i>Assign Role</a>
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
                    <h5 class="modal-title " id="modelHeading">@lang('title.add_new')</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                            <span id="errorContent"></span>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('name') }}" id="name" class="form-control" name="name">
                                <input type="hidden" name="role_id" id="role_id">
                            </div>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('display-name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" id="display_name" placeholder="{{ __('display-name') }}" class="form-control" name="display_name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('description') }}</label>
                            <div class="col-sm-9">
                                <textarea rows="3" cols="3" name="description" id="description" class="form-control" placeholder="{{ __('description') }}"></textarea>
                            </div>
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn" class="btn bg-primary">{{ __('add-new') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- /horizontal form modal -->

<script type="text/javascript">
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

    function editAction(slug, actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-role') }}");
                $('#errorbox').hide();
                $('#saveBtn').html("{{ __('edit-role') }}");
                $('#saveBtn').val("edit_role");
                $('#roleModel').modal('show');
                $('#role_id').val(data.data.slug);
                $('#name').val(data.data.name);
                $('#display_name').val(data.data.display_name);
                $('#description').val(data.data.description);
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
        $('#role_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('new-role') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_role");
        $('#saveBtn').html("{{ __('save-role') }}");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');
        var btnVal = $(this).val();
        var slug = $('#role_id').val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_role'){
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ url('roles') }}/"+slug+"/edit",
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
                                // $("#reloadDiv").load("{{ route('roles.index') }}");
                                location.reload();
                            });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.error);
                    $('#errorContent').html('');
                    $.each(err.error, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtn').html("{{ __('save-changes') }}");
                }
            });
        }
        else
        {
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('roles.store') }}",
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
                        
                            // $("#reloadDiv").load("{{ route('roles.index') }}");
                            location.reload();
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#errorbox').show();
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.error);
                        $('#errorContent').html('');
                        $.each(err.error, function(key, value) {
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
