@extends('layouts.app')

@section('content')


<div class="content">
    <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('outofzone-master') }}</h5>
                <div class="header-elements">
                    <div class="list-icons">
                        @if(auth()->user()->can('new-outofzone'))
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
                    <th>{{ __('km') }}</th>
                    <th>{{ __('price') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outofzonelist as $key => $list)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $list->kilometer !!}</td>
                        <td>₹ {!! $list->price !!}</td>
                        <td>
                            @if($list['status'] == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td>
                        <td>                               
                            <a class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7" ></i></a>
                                <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: relative; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                    @if(auth()->user()->can('edit-outofzone'))
                                    <a href="#" onclick="Javascript: return editAction(`{{ route('outofzoneedit',$list->id) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                   
                                    @endif
                                    @if(auth()->user()->can('delete-outofzone'))
                                    <a href="#" onclick="Javascript: return deleteAction('$list->id', `{{ route('outofzonedelete',$list->id) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                    @endif
                                    @if(auth()->user()->can('status-change-outofzone'))
                                    <a href="#" onclick="Javascript: return activeAction( `{{ route('outofzoneactive',  $list->id) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
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
                    <form id="roleForm" name="roleForm" class="form-horizontal">
                        @csrf

                        <div class="modal-body">
                            <div class="alert alert-danger" id="errorbox">
                                <button type="button" class="close"><span>×</span></button>
                                <span id="errorContent"></span>
                            </div>
                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('km') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('km') }}" id="kilometer" class="form-control" name="kilometer">
                                    <input type="hidden" name="id" id="id">
                                </div>
                            </div>
                          
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{{ __('price') }}</label>
                                <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('price') }}" id="price" class="form-control" name="price" >
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
               console.log(data);
                $('#modelHeading').html("{{ __('edit-outofzone-master') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit_user");
                $('#roleModel').modal('show');
                $('#id').val(data.outofzoneMaster.id);
                $('#kilometer').val(data.outofzoneMaster.kilometer);
                $('#price').val(data.outofzoneMaster.price);
                console.log(data.OutofzoneMaster.id);
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
        $('#modelHeading').html("{{ __('create-new-outofzone-master') }}");
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
                url: "{{ route('outofzoneupdate') }}",
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
                url: "{{ route('outofzonesave') }}",
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