@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-document') }}</h5>
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
                    <th>{{ __('document-name') }}</th>
                    <th>{{ __('required') }}</th>
                    <th>{{ __('identifier') }}</th>
                    <th>{{ __('experie-date') }}</th>
                    <th>{{ __('issue-date') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('group_by') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $key => $documents)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $documents->document_name !!}</td>
                        <td>@if($documents->requried == 1)
                                <span class="badge badge-success">{{ __('yes') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('no') }}</span>
                            @endif
                        </td> 
                        <td>@if($documents->identifier == 1)
                                <span class="badge badge-success">{{ __('yes') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('no') }}</span>
                            @endif
                        </td> 
                        <td>@if($documents->expiry_date == 1)
                                <span class="badge badge-success">{{ __('yes') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('no') }}</span>
                            @endif
                        </td> 
                        <td>@if($documents->expiry_date == 2)
                                <span class="badge badge-success">{{ __('yes') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('no') }}</span>
                            @endif
                        </td> 
                        <td>@if($documents->status == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td> 
                        <td>{{ $documents->getDocumentGroup ? $documents->getDocumentGroup->name :''}}
                        </td> 
                        <td>    
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-document'))
                                <a href="#" onclick="Javascript: return editAction(`{{ route('documentsEdit',$documents->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                @endif
                                @if(auth()->user()->can('delete-document'))
                                <a href="#" onclick="Javascript: return deleteAction('$documents->slug', `{{ route('documentsDelete',$documents->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                @endif
                                @if(auth()->user()->can('active-document'))
                                <a href="#" onclick="Javascript: return activeAction(`{{ route('documentsActive',$documents->slug) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="document_slug" id="document_slug" />
                    <div class="modal-body">
                        <div class="alert alert-danger" id="errorbox">
                            <button type="button" class="close"><span>×</span></button>
                            <span id="errorContent"></span>
                        </div>
                        <table class="table">
                            <tr>
                                <th>{{ __('document-name') }}</th>
                                <th>{{ __('required') }}</th>
                                <th>{{ __('identifier') }}</th>
                                <th>{{ __('experie-date') }}</th>
                                <th>{{ __('issue-date') }}</th>
                                <th>{{ __('group_by') }}</th>
                                <th><button type="button" class="btn bg-green-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" id="add_row" data-original-title="Add"> <i class="icon-plus3"></i> </button></th>
                            </tr>
                            <tbody id="tbody">
                                <tr><td><input type="text" placeholder="Document Name" id="document_name" class="form-control" name="document[]" /></td><td><label class="">
											<input type="checkbox" class="required" id="0" name="required" value="1">
                                            <input type="hidden" value="0" name="required_value[]" id="required_value_0" />
											Yes
										</label></td><td><label class="">
											<input type="checkbox" id="0" class="identifier" name="identifier" value="1">
                                            <input type="hidden" value="0" name="identifier_value[]" id="identifier_value_0" />
											Yes
										</label>
                                        </td><td><label class="">
											<input type="radio" id="0" class="experied experied_value" name="experied_check[0]" value="1">
											Yes
										</label>
                                        </td><td><label class="">
											<input type="radio" id="0" class="experied issed_value" name="experied_check[0]" value="2">
											Yes
										</label><input type="hidden" value="0" name="experied[0]" id="experi_value_0" /></td>
                                        <td><label class="">
											 <select id="group_by"  name="group_by[]" class="form-control" style="width:150px;">
                                                  <option value="">Select GroupBy</option>
                                                @foreach($document_group as $groups)
                                                  <option value="{{ $groups->id }}">{{ $groups->name }}</option>
                                                @endforeach
                                             </select>
										</label></td>
                                        <td><button type="button" disabled class="btn bg-danger-400 btn-icon rounded-round legitRipple delete_row" data-popup="tooltip" title="" data-placement="bottom" id="" data-original-title="Delete"> <i class="icon-close2"></i> </button></td></tr>
                            </tbody>
                        </table>
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
                $('#document_name').val(data.data.document_name);
                $('#group_by').val(data.data.group_by);
                if(data.data.requried == 1){
                    $('.required').prop('checked',true);
                }
                else{
                    $('.required').prop('checked',false);
                }
                if(data.data.identifier == 1){
                    $('.identifier').prop('checked',true);
                }
                else{
                    $('.identifier').prop('checked',false);
                }
                if(data.data.expiry_date == 1){
                    $('.experied_value').prop('checked',true);
                }
                else if(data.data.expiry_date == 2){
                    $('.issed_value').prop('checked',true);
                }
                else{
                    $('.experied_value').prop('checked',false);
                    $('.issed_value').prop('checked',false);
                }
                $('#required_value_0').val(data.data.requried);
                $('#identifier_value_0').val(data.data.identifier);
                $('#experi_value_0').val(data.data.expiry_date);
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    $("#add_row").on('click',function(){
        var text = '<tr><td><input type="text" placeholder="Document Name" id="document" class="form-control" name="document[]" /></td><td><label class=""><input type="checkbox" class="required" id="'+i+'" name="required" value="1"><input type="hidden" value="0" name="required_value[]" id="required_value_'+i+'" /> Yes</label></td><td><label class=""><input type="checkbox" id="'+i+'" class="identifier" name="identifier" value="1"><input type="hidden" value="0" name="identifier_value[]" id="identifier_value_'+i+'" /> Yes</label></td><td><label class=""><input type="radio" id="'+i+'" class="experied" name="experied_check['+i+']" value="1"> Yes</label></td><td><label class=""><input type="radio" id="'+i+'" name="experied_check['+i+']" class="experied" value="2"> Yes</label><input type="hidden" value="0" name="experied['+i+']" id="experi_value_'+i+'" /></td><td><label class=""><select id="'+i+'"  name="group_by[]"><option value="">Select GroupBy</option>@foreach($document_group as $groups)<option value="{{ $groups->id }}">{{ $groups->name }}</option>@endforeach</select></label></td><td><button type="button" class="btn bg-danger-400 btn-icon rounded-round legitRipple delete_row" data-popup="tooltip" title="" data-placement="bottom" id="" data-original-title="Delete"> <i class="icon-close2"></i> </button></td></tr>';

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
                url: "{{ route('documentsUpdate') }}",
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
                url: "{{ route('documentsSave') }}",
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
