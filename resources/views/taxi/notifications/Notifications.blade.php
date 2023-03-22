@extends('layouts.app')

@section('content')
<style>
    .form-group.required .col-form-label:after {
                content:" *";
                color: red;
                weight:100px;
            }

</style>
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">


<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-push-notification') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <!-- @if(auth()->user()->can('new-notification'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                    @endif -->
                    @if(auth()->user()->can('new-driver'))
                        <a href="{{ route('notificationAdd') }}" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</a>
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
                    <th>{{ __('title') }}</th>
                    <th>{{ __('sub_title') }}</th>
                    <th>{{ __('date') }}</th>
                    <th>{{ __('image') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notification as $key => $notifications)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $notifications->title!!}</td>
                        <td>{!! $notifications->sub_title!!}</td>
                        <td>{!! date('d-M-y', strtotime($notifications->date))!!}</td>
                        <td>
                            <img src="{{$notifications->images1}}" height="40px" width="auto" alt="" />
                        </td>  
                        <td>        
                            @if(auth()->user()->can('delete-notification'))
                                <a href="" class="btn bg-purple-400 btn-icon rounded-round legitRipple" onclick="Javascript: return deleteAction('$notifications->slug', `{{ route('notificationDelete',$notifications->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete"> <i class="icon-trash"></i> </a>
                            @endif 
                        </td>
  
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Horizontal form modal -->
    <!-- <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" class="form-horizontal">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                          
                            <span id="errorContent"></span>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('users') }}</label>
                            <div class="col-sm-9">
                                <select id="users_id" class="form-control" multiple="multiple" name="users[]">
                                    @foreach($users as $values)
                                    <option value="{{$values->slug}}">{{$values->firstname}} {{$values->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('drivers') }}</label>
                            <div class="col-sm-9">
                                <select id="driver_id" class="form-control" multiple="multiple" name="drivers[]">
                                    @foreach($drivers as $values)
                                    <option value="{{$values->slug}}">{{$values->firstname}} {{$values->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('title') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="title" id="title" class="form-control"  placeholder="{{ __('title') }}" >
                                <input type="hidden" name="notification_id" id="notification_id">
                            </div>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('sub_title') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="sub_title" id="sub_title" class="form-control"  placeholder="{{ __('sub_title') }}" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('has_redirect_url') }}</label>
                            <div class="col-sm-9">
                                <select id="has_redirect_url" class="form-control" name="has_redirect_url">
                                    <option value="">{{ __('select_has_redirect_url') }}</option>
                                    <option value="yes">{{ __('yes') }}</option>
                                    <option value="no">{{ __('no') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row hiddens">
                            <label class="col-form-label col-sm-3">{{ __('redirect_url') }}</label>
                            <div class="col-sm-9">
                                <input type="input" placeholder="{{ __('redirect_url') }}" id="redirect_url" class="form-control" name="redirect_url">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('image1') }}</label>
                            <div class="col-sm-9">
                                <input type="file" placeholder="{{ __('image1') }}" id="image1" class="form-control" name="image1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('message') }}</label>
                            <div class="col-sm-3">
                                <textarea id="message" cols="50" row="10"></textarea>
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
    </div> -->

</div>
<!-- /horizontal form modal -->

<script type="text/javascript">
$(".hiddens").hide();

  $(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#has_redirect_url").on('change',function(){
        var value = $(this).val();
        if(value == "yes"){
            $(".hiddens").show();
        }
        else{
            $(".hiddens").hide();
        }
    })


    $('#add_new_btn').click(function () {
        $('#notification_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-push-notification') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_notification");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('image1',$('#image1').prop('files').length > 0 ? $('#image1').prop('files')[0] : '');
        // formData.append('image2',$('#image2').prop('files').length > 0 ? $('#image2').prop('files')[0] : '');
        // formData.append('image3',$('#image3').prop('files').length > 0 ? $('#image3').prop('files')[0] : '');
        formData.append('users_id',$('#users_id').val());
        formData.append('driver_id',$('#driver_id').val());
        formData.append('title',$('#title').val());
        formData.append('sub_title',$('#sub_title').val());
        formData.append('has_redirect_url',$('#has_redirect_url').val());
        formData.append('redirect_url',$('#redirect_url').val());
        formData.append('message',$('#message').val());
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
        $.ajax({
            data: formData,
            url: "{{ route('notificationSave') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                if(data.message == "success"){
                    $('#roleForm').trigger("reset");
                    $('#roleModel').modal('hide');
                    swal({
                        title: "{{ __('data-added') }}",
                        text: "{{ __('data-added-successfully') }}",
                        icon: "success",
                    }).then((value) => {                            
                        $("#reloadDiv").load("{{ route('notification') }}");
                    });
                }
                else{
                    $('#errorbox').show();
                    $('#errorContent').html('');
                    $('#errorContent').append('<strong><li>'+data.message+'</li></strong>');
                    $('#saveBtn').html("{{ __('save-changes') }}");
                }                
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
</script>
<script src="{{ asset('backend/assets/js/jquery.multiselect.js') }}"></script>
<script>
$('#driver_id').multiselect({
                    columns: 1,
                    placeholder: 'Select Drivers List',
                    search: true,
                    selectAll: true
                });
$('#users_id').multiselect({
                    columns: 1,
                    placeholder: 'Select Users List',
                    search: true,
                    selectAll: true
                });
</script>

@endsection
