@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('group_document') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-document'))
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
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $key => $documents)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $documents->name !!}</td>
                        <td>@if($documents->status == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td> 
                        <td>    
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-document'))
                                <a href="#" onclick="Javascript: return editAction(`{{ route('group-documentsEdit',$documents->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                @endif
                                @if(auth()->user()->can('delete-document'))
                                <a href="#" onclick="Javascript: return deleteAction('$documents->slug', `{{ route('group-documentsDelete',$documents->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                @endif
                                @if(auth()->user()->can('active-document'))
                                <a href="#" onclick="Javascript: return activeAction(`{{ route('group-documentsActive',$documents->slug) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
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
                            <label class="col-form-label col-sm-2">{{ __('name') }}</label>
                            <div class="col-sm-10">
                               <input type="text" placeholder="Document Name" id="name" class="form-control" name="document[]" />
                               <input type="hidden" name="document_slug" id="document_slug" /> 
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                            <button type="submit" id="saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- /horizontal form modal -->

<script type="text/javascript">
    var i =1;
    function editAction(actionUrl){
        $(".delete_row").click();
        $("#add_row").hide();
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-document') }}");
                $('#errorbox').hide();
                // $('#saveBtn').html("Edit Complaint");
                $('#saveBtn').val("edit_document");
                $('#roleModel').modal('show');
                $('#document_slug').val(data.data.slug);
                $('#name').val(data.data.name);
                
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    $("#add_row").on('click',function(){
        var text = '<tr><td><input type="text" placeholder="Document Name" id="document" class="form-control" name="document[]" /></td><td><label class=""><input type="checkbox" class="required" id="'+i+'" name="required" value="1"><input type="hidden" value="0" name="required_value[]" id="required_value_'+i+'" /> Yes</label></td><td><label class=""><input type="checkbox" id="'+i+'" class="identifier" name="identifier" value="1"><input type="hidden" value="0" name="identifier_value[]" id="identifier_value_'+i+'" /> Yes</label></td><td><label class=""><input type="radio" id="'+i+'" class="experied" name="experied_check['+i+']" value="1"> Yes</label></td><td><label class=""><input type="radio" id="'+i+'" name="experied_check['+i+']" class="experied" value="2"> Yes</label><input type="hidden" value="0" name="experied['+i+']" id="experi_value_'+i+'" /></td><td><button type="button" class="btn bg-danger-400 btn-icon rounded-round legitRipple delete_row" data-popup="tooltip" title="" data-placement="bottom" id="" data-original-title="Delete"> <i class="icon-close2"></i> </button></td></tr>';

        $("#tbody").append(text);
        i++;
    })

    $(document).on('click',".delete_row",function(){
        var row = $(this).parents('tr');
        $(row).remove();
    })

    $(document).on('click',".required",function(){
        var id = $(this).attr('id');
        if($(this).is(":checked")){
            $("#required_value_"+id).val('1');
        }
        else{
            $("#required_value_"+id).val('0');
        }
    })

    $(document).on('click',".identifier",function(){
        var id = $(this).attr('id');
        if($(this).is(":checked")){
            $("#identifier_value_"+id).val('1');
        }
        else{
            $("#identifier_value_"+id).val('0');
        }
    })
    $(document).on('click',".experied",function(){
        var id = $(this).attr('id');
        if($(this).is(":checked")){
            $("#experi_value_"+id).val($(this).val());
        }
        else{
            $("#experi_value_"+id).val($(this).val());
        }
    })
  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });


    $('#add_new_btn').click(function () {
        $('#complaint_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-document') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_document");
        // $('#saveBtn').html("Save Complaint");
        $('#errorbox').hide();
        $("#add_row").show();
        $(".delete_row").click();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_document'){
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('group-documentsUpdate') }}",
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
                                // $("#reloadDiv").load("{{ route('documents') }}");
                                location.reload();
                            });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.error);
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
                url: "{{ route('group-documentsSave') }}",
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
                            
                                // $("#reloadDiv").load("{{ route('documents') }}");
                                location.reload();
                            });
                        
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#errorbox').show();
                        var err = eval("(" + xhr.responseText + ")");
                        // console.log(err.error);
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
<script type="text/javascript">
    var i =1;
    var message = "{{session()->get('message')}}";

    if(message){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

</script>

@endsection
