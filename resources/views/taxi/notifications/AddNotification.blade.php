@extends('layouts.app')
@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css" rel="stylesheet" />



<style>

    .select2-container{
        width: 100% !important;
    }

    .select2-selection{
        max-height: 0;
        overflow: hidden;
        overflow-y: scroll;
    }
</style>

<div class="content">
     
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-driver') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a href="{{ route('notification') }}" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-arrow-left7 mr-2"></i> {{ __('back') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body"> 
            <fieldset class="mb-3">
                <form id="roleForm" name="roleForm" class="form-horizontal">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
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
                            
                            <div class="card-body p-0">
                                <div class="overflow-auto mw-100">
                                    <textarea class="summernote summernote-borderless" name="message" id="message"></textarea>
                                </div>  
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                    </div>
                </form>
			</fieldset>
        </div>
    </div>

</div>


<script type="text/javascript">
     $("#errorbox").hide();
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
<script>
 $('#message').emojioneArea({
    pickerPosition:'bottom'
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
