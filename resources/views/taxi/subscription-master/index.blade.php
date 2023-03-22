@extends('layouts.app')

@section('content')


<div class="content">
    <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('subscription-master') }}</h5>
                <div class="header-elements">
                    <div class="list-icons">
                        @if(auth()->user()->can('new-subscription'))
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
                    <th>{{ __('name') }}</th>
                    <th>{{ __('amount') }}</th>
                    <th>{{ __('validity') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submasterlist as $key => $sublist)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $sublist->name !!}</td>
                        <td>{{$currency}}{!! $sublist->amount !!}</td> 
                        <td>{!! $sublist->validity !!} days</td> 
                        <td>                               
                            <a class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7" ></i></a>
                                <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: relative; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                    @if(auth()->user()->can('edit-subscription'))
                                    <a href="#" onclick="Javascript: return editAction(`{{ route('submasteredit',$sublist->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                    <!-- <a href="" class="btn bg-pink-400 btn-icon rounded-round legitRipple" onclick="Javascript: return editAction(`{{ route('usersEdit',$sublist->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit"> <i class="icon-pencil"></i> </a> -->
                                    @endif
                                    @if(auth()->user()->can('delete-subscription'))
                                    <a href="#" onclick="Javascript: return deleteAction('$sublist->slug', `{{ route('submasterdelete',$sublist->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                    <!-- <a href="" class="btn bg-purple-400 btn-icon rounded-round legitRipple" onclick="Javascript: return deleteAction('$sublist->slug', `{{ route('usersDelete',$sublist->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete"> <i class="icon-trash"></i> </a> -->
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
                                <label class="col-form-label col-sm-3 ">{{ __('name') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('name') }}" id="name" class="form-control" name="name">
                                    <input type="hidden" name="user_id" id="user_id">
                                </div>
                            </div>
                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3">{{ __('amount') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('amount') }}" id="amount" class="form-control" name="amount">
                                </div>
                            </div>
                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3">{{ __('validity') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('validity_days') }}" id="validity" class="form-control" name="validity">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{{ __('description') }}</label>
                                <div class="col-sm-9">
                                    <textarea placeholder="{{ __('description') }}" id="description" class="form-control" name="description" ></textarea>
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
               //console.log(data);
                $('#modelHeading').html("{{ __('edit-subscription') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit_user");
                $('#roleModel').modal('show');
                $('#user_id').val(data.sublist.slug);
                $('#name').val(data.sublist.name);
                $('#amount').val(data.sublist.amount);
                $('#validity').val(data.sublist.validity);
                $('#description').val(data.sublist.description);
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
        $('#modelHeading').html("{{ __('create-new-subscription') }}");
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
                url: "{{ route('submasterupdate') }}",
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
                url: "{{ route('submastersave') }}",
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