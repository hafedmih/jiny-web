@extends('layouts.app')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	

	<!-- Global stylesheets -->
    
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="../../../../global_assets/css/icons/icomoon/styles.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap_limitless.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/layout.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="../../../../global_assets/js/main/jquery.min.js"></script>
	<script src="../../../../global_assets/js/main/bootstrap.bundle.min.js"></script>
	<script src="../../../../global_assets/js/plugins/loaders/blockui.min.js"></script>
	<script src="../../../../global_assets/js/plugins/ui/ripple.min.js"></script>
	<!-- /core JS files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/fontawesome.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>



	<!-- Theme JS files -->
    <script src="{{ asset('backend/global_assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
	<script src="../../../../global_assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<script src="../../../../global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="../../../../global_assets/js/demo_pages/mail_list_write.js"></script>
	<!-- /theme JS files -->

</head>
<div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-email') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a href="{{ route('email') }}" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-arrow-left7 mr-2"></i> {{ __('back') }}</a>
                </div>
            </div>
        </div>
    </div>
<div class="card">
        <div class="card-body">
            
            <div class="tab-content">
                <div class="tab-pane fade show active" id="top-justified-tab1">
                    <fieldset class="mb-3">
                        <form method="post" id="roleForm">
                            @csrf              
                            <div class="modal-body">
                                <div class="alert alert-danger alert-dismissible" id="errorbox">
                                    <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                                    <span id="errorContent"></span>
                                </div>          
                                <div class="row">
                                <div class="form-group col-md-6">
                                                            <label class="col-form-label">{{ __('users') }}</label>
                                                                <select id="users_id" class="form-control" multiple="multiple" name="users[]">
                                                                    @foreach($users as $values)
                                                                        <option value="{{$values->slug}}">{{$values->firstname}} {{$values->lastname}}</option>
                                                                    @endforeach
                                                                </select>
                                                        </div>
                                                        <div class="form-group col-md-6 ">
                                                            <label class="col-form-label">{{ __('drivers') }}</label>
                                                                <select id="driver_id" class="form-control" multiple="multiple" name="drivers[]">
                                                                    @foreach($drivers as $values)
                                                                        <option value="{{$values->slug}}">{{$values->firstname}} {{$values->lastname}}</option>
                                                                    @endforeach
                                                                </select>
                                                        </div>
                                </div>      
                                <div class="row">
                                    <div class="form-group col-md-6 ">
                                        <label class="col-form-label">{{ __('Subject') }}</label>
                                        <input name="subject" id="subject" type="text" class="form-control py-2 px-0 border-0 rounded-0" placeholder="Add subject">
                                    </div>
                                    <!-- <div class="form-group col-md-6">
                                        <label class="col-form-label">{{ __('attachments')}}</label>
                                        <input type="file" id="attach" name="attach">
                                    </div> -->
                                </div>                  
                                <!-- <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="row">
                                                        <div class="form-group col-md-6 required">
                                                            <label class="col-form-label">{{ __('users') }}</label>
                                                                <select id="users_id" class="form-control" multiple="multiple" name="users[]">
                                                                    @foreach($users as $values)
                                                                        <option value="{{$values->slug}}">{{$values->firstname}} {{$values->lastname}}</option>
                                                                    @endforeach
                                                                </select>
                                                        </div>
                                                        <div class="form-group col-md-6 required">
                                                            <label class="col-form-label">{{ __('drivers') }}</label>
                                                                <select id="driver_id" class="form-control" multiple="multiple" name="drivers[]">
                                                                    @foreach($drivers as $values)
                                                                        <option value="{{$values->slug}}">{{$values->firstname}} {{$values->lastname}}</option>
                                                                    @endforeach
                                                                </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="align-top py-0">
                                                    <div class="py-2 mr-sm-3">Subject:</div>
                                                </td>
                                                <td class="align-top py-0">
                                                    <input name="subject" id="subject" type="text" class="form-control py-2 px-0 border-0 rounded-0" placeholder="Add subject">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div> -->
                                    <!-- /mail details -->
                                    <!-- Mail container -->
                                    <div class="card-body p-0">
                                        <div class="overflow-auto mw-100">
                                            <textarea class="summernote summernote-borderless" name="content" id="content"></textarea>
                                        </div>                                 
                                    
                                <div class="text-right">
                                <button type="button" id="saveBtn" class="btn btn-primary legitRipple">Submit <i class="icon-paperplane ml-2"></i></button>
                                </div>                                
                                </div>
                            </div>     
                        </form>
                    </fieldset>
                </div>

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


                $(document).ready(function() {
  $('.summernote').summernote();
});
</script>
<script type="text/javascript">
    $("#errorbox").hide();
    $(".hiddens").hide();

  $(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // $("#has_redirect_url").on('change',function(){
    //     var value = $(this).val();
    //     if(value == "yes"){
    //         $(".hiddens").show();
    //     }
    //     else{
    //         $(".hiddens").hide();
    //     }
    // })


    $('#add_new_btn').click(function () {
        $('#email_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-email') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_email");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault(e);
        var formData = new FormData();
        // formData.append('attachments',$('#attach').prop('files').length > 0 ? $('#attach').prop('files')[0] : '');
        formData.append('users_id',$('#users_id').val());
        formData.append('driver_id',$('#driver_id').val());
        formData.append('subject',$('#subject').val());
        formData.append('content',$('#content').val());
        // console.log($('#content').val());
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
        $.ajax({
            data: formData,
            url: "{{ route('emailSave') }}",
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
                        $("#reloadDiv").load("{{ route('email') }}");
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
<!-- <script>
    var ajax_call = function() {
        Swal.fire({
            title:'Welcome to the world of RODA',
            showConfirmButton: false,
            // color: '#eddb11',
            position: 'top-end',
            timer:4000,  
        })
};

var interval = 1000 * 60 * 1; // where X is your every X minutes

setInterval(ajax_call, interval);
</script> -->
<!-- <script>
    swal({
    title: "Wow!",
    showConfirmButton: false,
    timer:5000,
    delay:5000
}).then(function() {
    // location.reload(true);
    swal({
    title: "Wow!",
    text: "Message!",
    type: "success",
    timer:5000
});
});
</script> -->
@endsection