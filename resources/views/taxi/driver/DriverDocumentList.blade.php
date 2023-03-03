@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-driver-document') }} </h5>
            <div class="header-elements">
                <div class="list-icons">
                {{ __('name') }} : {{$user->firstname}} {{$user->lastname}}
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
                    <th>{{ __('image') }}</th>
                    <th>{{ __('document-status') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($document as $key => $docum)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $docum->document_name !!}</td>
                        <td><img src="{{$docum->document_image}}" height="40px" width="auto" alt="" /></td>
                        <td>@if($docum->document_status == 2)
                                <span class="badge badge-primary">{{ __('approved') }}</span>
                            @elseif($docum->document_status == 0)
                                <span class="badge badge-danger">{{ __('denaited') }}</span>
                            @elseif($docum->document_status == 1)
                                <span class="badge badge-info">{{ __('not-approved') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('not-uploaded') }}</span>
                            @endif
                        </td> 
                        <td>@if($docum->status == 1)
                                <span class="badge badge-primary">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td> 
                        <td>                     
                            @if(auth()->user()->can('edit-driver-document'))
                                <a href="" class="btn bg-pink-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" onclick="Javascript: return updateAction(`../driver-document-edit/{{$user->slug.'/'.$docum->slug}}`)" data-placement="bottom" data-original-title="Edit"> <i class="icon-"></i> </a>
                            @endif
                            @if(auth()->user()->can('approved-driver-document'))
                                <button class="btn btn-outline bg-brown-400 text-brown-800 btn-icon rounded-round legitRipple approved" for="approved_{{$docum->slug}}" data-popup="tooltip" title="" data-placement="bottom" data-original-title="change status" ><i class="icon-user-check"></i><input type="checkbox" id="approved_{{$docum->slug}}" name="approved[]" class="hide" value="{{$docum->slug}}"></button>
                            @endif
                            @if(auth()->user()->can('denait-driver-document'))
                                <button class="btn btn-outline bg-danger-400 text-danger-800 btn-icon rounded-round legitRipple denaited" for="denait_{{$docum->slug}}" data-popup="tooltip" title="" data-placement="bottom" data-original-title="change status" ><i class="icon-trash"></i><input type="checkbox" id="denait_{{$docum->slug}}" name="denaited[]" class="hide" value="{{$docum->slug}}"></button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="card-header header-elements-inline">
            <h5 class="card-title"></h5>
            <div class="header-elements">
                <div class="list-icons">
                    <button type="button" class="btn btn-primary legitRipple" id="upproved" >Updated <i class="icon-paperplane ml-2"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" action="{{ route('driverDocumentUpdate') }}" method="post" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent"></span>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-form-label">{{ __('title') }}</label>
                                <input type="text" name="title" id="title" class="form-control"  placeholder="{{ __('title') }}" >
                                <input type="hidden" name="document_id" id="document_id">
                                <input type="hidden" name="date_required" id="date_required">
                                <input type="hidden" name="driver_id" id="driver_id" value="{{$user->slug}}">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="col-form-label">{{ __('image1') }}</label>
                                <input type="file" id="document_image" class="form-control" name="document_image">
                                <img src="" height="40px" width="auto" alt="" id="image" />
                            </div>

                            <div class="form-group col-md-6 dated">
                                <label class="col-form-label date_lable">{{ __('experie-date') }}</label>
                                <input type="date" id="expiry_date" class="form-control" name="expiry_date">
                            </div>
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
    var i =1;
    var approved = [];
    var denaited = [];
    $(".dated").hide();
    $(".hide").hide();

    function updateAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $('#roleForm').trigger("reset");
               console.log(data);
                $('#modelHeading').html("{{ __('edit-document') }}");
                $('#errorbox').hide();
                // $('#saveBtn').html("Edit Complaint");
                $('#saveBtn').val("edit_document");
                $('#roleModel').modal('show');
                $('#document_id').val(data.data.slug);
                $('#title').val(data.data.document_name);
                $('#expiry_date').val(data.data.expiry_dated);
                $('#image').attr("src",data.data.document_image);
                if(data.data.expiry_date == 1){
                    $(".dated").show();
                    $('.date_lable').html("{{ __('experie-date') }}");
                    $('#expiry_date').val(data.data.expiry_dated);
                }
                else if(data.data.expiry_date == 2){
                    $(".dated").show();
                    $('.date_lable').html("{{ __('issue-date') }}");
                    $('#expiry_date').val(data.data.issue_dated);
                }
                else{
                    $(".dated").hide();
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    $('#document_image').change(function(){
          let reader = new FileReader();
          reader.onload = (e) => { 
            $("#image").attr('src',e.target.result);
          }
          reader.readAsDataURL(this.files[0]); 
    });

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $(".approved").click(function(){
        var id = $(this).attr('for');
        if(approved.indexOf($("#"+id).val()) == -1){
            approved = [];
            $(this).removeClass('btn-outline');
            $("#"+id).prop('checked', true);
            $('input[name="approved[]"]:checkbox:checked').each(function(i){
              approved[i] = $(this).val();
            });
            console.log(approved);
        }
        else{
            approved = [];
            $(this).addClass('btn-outline');
            $("#"+id).prop('checked', false);
            $('input[name="approved[]"]:checkbox:checked').each(function(i){
              approved[i] = $(this).val();
            });
            console.log(approved);
        }
    });

    $(".denaited").click(function(){
        var id = $(this).attr('for');
        if(denaited.indexOf($("#"+id).val()) == -1){
            denaited = [];
            $(this).removeClass('btn-outline');
            $("#"+id).prop('checked', true);
            $('input[name="denaited[]"]:checkbox:checked').each(function(i){
              denaited[i] = $(this).val();
            });
            console.log(denaited);
        }
        else{
            denaited = [];
            $(this).addClass('btn-outline');
            $("#"+id).prop('checked', false);
            $('input[name="denaited[]"]:checkbox:checked').each(function(i){
              denaited[i] = $(this).val();
            });
            console.log(denaited);
        }
    });

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('document_image',$('#document_image').prop('files').length > 0 ? $('#document_image').prop('files')[0] : '');
        formData.append('document_id',$('#document_id').val());
        formData.append('driver_id',$('#driver_id').val());
        formData.append('date_required',$('#date_required').val());
        formData.append('expiry_date',$('#expiry_date').val());
        $(this).html("{{ __('sending') }}");
        $("#errorbox").hide();
        $.ajax({
            data: formData,
            url: "{{ route('driverDocumentUpdate') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                swal({
                    title: "{{ __('data-added') }}",
                    text: "{{ __('data-added-successfully') }}",
                    icon: "success",
                }).then((value) => {        
                    window.location.href = "../driver-document/"+$('#driver_id').val();
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
    });



    $('#upproved').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('approved_document_id',approved);
        formData.append('denaited_document_id',denaited);
        formData.append('driver_id',$('#driver_id').val());
        $.ajax({
            data: formData,
            url: "{{ route('driverDocumentApproved') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                swal({
                    title: "{{ __('data-added') }}",
                    text: "{{ __('data-added-successfully') }}",
                    icon: "success",
                }).then((value) => {        
                    window.location.href = "{{ route('driver') }}";
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
    });
</script>

@endsection
