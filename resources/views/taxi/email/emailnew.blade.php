@extends('layouts.app')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Limitless - Responsive Web Application Kit by Eugene Kopyov</title>

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




	<!-- Theme JS files -->
    <script src="{{ asset('backend/global_assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
	<script src="../../../../global_assets/js/plugins/editors/summernote/summernote.min.js"></script>
	<script src="../../../../global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="../../../../global_assets/js/demo_pages/mail_list_write.js"></script>
	<!-- /theme JS files -->

</head>
<!-- Main content -->
<div class="content-wrapper">

<!-- Page header -->
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Mailbox</span> - Write Mail</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <form action="#">
                <div class="form-group form-group-feedback form-group-feedback-right">
                    <input type="search" class="form-control wmin-200" placeholder="Search messages">
                    <div class="form-control-feedback">
                        <i class="icon-search4 font-size-base text-muted"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="index-2.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                <a href="mail_write.html" class="breadcrumb-item">Mailbox</a>
                <span class="breadcrumb-item active">Write mail</span>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="breadcrumb justify-content-center">
                <a href="#" class="breadcrumb-elements-item">
                    <i class="icon-comment-discussion mr-2"></i>
                    Support
                </a>

                <div class="breadcrumb-elements-item dropdown p-0">
                    <a href="#" class="breadcrumb-elements-item dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-gear mr-2"></i>
                        Settings
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item"><i class="icon-user-lock"></i> Account security</a>
                        <a href="#" class="dropdown-item"><i class="icon-statistics"></i> Analytics</a>
                        <a href="#" class="dropdown-item"><i class="icon-accessibility"></i> Accessibility</a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item"><i class="icon-gear"></i> All settings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /page header -->


<!-- Content area -->
<div class="content">

    <!-- Inner container -->
    <div class="d-md-flex align-items-md-start">

        <!-- Left sidebar component -->
        <div class="sidebar sidebar-light bg-transparent sidebar-component sidebar-component-left border-0 shadow-0 sidebar-expand-md">

            <!-- Sidebar content -->
            <div class="sidebar-content">

                <!-- Actions -->
                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <span class="text-uppercase font-size-sm font-weight-semibold">Actions</span>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <a href="#" class="btn bg-indigo-400 btn-block">Compose mail</a>
                    </div>
                </div>
                <!-- /actions -->


                <!-- Sub navigation -->
                <div class="card">
                    <div class="card-header bg-transparent header-elements-inline">
                        <span class="text-uppercase font-size-sm font-weight-semibold">Navigation</span>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <ul class="nav nav-sidebar mb-2" data-nav-type="accordion">
                            <li class="nav-item-header">Folders</li>
                            <li class="nav-item">
                                <a href="#" class="nav-link active">
                                    <i class="icon-drawer-in"></i>
                                    Inbox
                                    <span class="badge bg-success badge-pill ml-auto">32</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="icon-drawer3"></i> Drafts</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="icon-drawer-out"></i> Sent mail</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="icon-stars"></i> Starred</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="icon-spam"></i>
                                    Spam
                                    <span class="badge bg-danger badge-pill ml-auto">99+</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="icon-bin"></i> Trash</a>
                            </li>
                            <li class="nav-item-header">Labels</li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><span class="badge badge-mark border-primary align-self-center mr-3"></span> Facebook</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><span class="badge badge-mark border-success align-self-center mr-3"></span> Spotify</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><span class="badge badge-mark border-indigo-400 align-self-center mr-3"></span> Twitter</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><span class="badge badge-mark border-pink-400 align-self-center mr-3"></span> Dribbble</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-fill overflow-auto">

							<!-- Single mail -->
							<div class="card">

								<!-- Action toolbar -->
								<div class="navbar navbar-light navbar-expand-lg shadow-0 py-lg-2 rounded-top">
									<div class="text-center d-lg-none w-100">
										<button type="button" class="navbar-toggler w-100 h-100" data-toggle="collapse" data-target="#inbox-toolbar-toggle-write">
											<i class="icon-circle-down2"></i>
										</button>
									</div>

									<div class="navbar-collapse text-center text-lg-left flex-wrap collapse" id="inbox-toolbar-toggle-write">

										<div class="mt-3 mt-lg-0 mr-lg-3">
											<button type="submit" id="saveBtn" class="btn bg-blue"><i class="icon-paperplane mr-2"></i> Submit</button>
										</div>


										<div class="mt-3 mt-lg-0 mr-lg-3">
											<div class="btn-group">
												<button type="button" class="btn btn-light">
													<i class="icon-checkmark3"></i>
													<span class="d-none d-lg-inline-block ml-2">Save</span>
												</button>
												<button type="button" class="btn btn-light">
													<i class="icon-cross2"></i>
													<span class="d-none d-lg-inline-block ml-2">Cancel</span>
												</button>
					                    		<div class="btn-group">
													<button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></button>
													<div class="dropdown-menu dropdown-menu-right">
														<a href="#" class="dropdown-item">Select all</a>
														<a href="#" class="dropdown-item">Select read</a>
														<a href="#" class="dropdown-item">Select unread</a>
														<div class="dropdown-divider"></div>
														<a href="#" class="dropdown-item">Clear selection</a>
													</div>
												</div>
											</div>
										</div>

										<div class="navbar-text ml-lg-auto">12:49 pm</div>

										<div class="ml-lg-3 mb-3 mb-lg-0">
											<div class="btn-group">
												<button type="button" class="btn btn-light">
													<i class="icon-printer"></i>
													<span class="d-none d-lg-inline-block ml-2">Print</span>
												</button>
						                    	<button type="button" class="btn btn-light">
						                    		<i class="icon-new-tab2"></i>
						                    		<span class="d-none d-lg-inline-block ml-2">Share</span>
					                    		</button>
											</div>
										</div>
									</div>
								</div>
								
            <div class="table-responsive">
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
								<input type="text" class="form-control py-2 px-0 border-0 rounded-0" placeholder="Add subject">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
								<!-- /mail details -->
                                <!-- Mail container -->
								<div class="card-body p-0">
									<div class="overflow-auto mw-100">
										<div class="summernote summernote-borderless">

											<!-- <div class="note-editing-area">
                                                <div class="note-editable card-block" contenteditable="true" role="textbox" aria-multiline="true" spellcheck="true">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                                                        <tr>
                                                            <td></td>
                                                        </tr>
                                                    </table>                                                  

                                                </div>

                                            </div> -->

										</div>
									</div>
								</div>
								<!-- /mail container -->

    </div>        
            <!-- <div class="summernote">
                
            </div> -->
      
                                </div>
                                <!-- Mail details -->
								
                <!-- /sub navigation -->
                <!-- Mail container -->
								


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
<!-- <script type="text/javascript">
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
        $('#email_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-email') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_notification");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault(e);
        // $(".addSkills").click(function(){
        // var formData = new FormData();
        // formData.append('users_id',$('#users_id').val());
        // formData.append('driver_id',$('#driver_id').val());
        // formData.append('subject',$('#subject').val());
        // formData.append('content',$('#content').val());
             var newdata = {
                'users_id' : $('#users_id').val();
                'driver_id': $('#driver_id').val();
                'subject': $('#subject').val();
                'content':$('#content').val();
             }   
        
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
        $.ajax({
            data: newdata,
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
</script> -->
<script>
    $(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

  $("#saveBtn").click(function(){
    $.ajax({
        dataType: 'json',
        type : 'post',
        url : "{{ route('emailSave') }}",

        // data:{ 
        //         'users_id': $('#user_id').val(),
        //         'driver_id': $('#driver_id').val(),
        //         'subject': $('#subject').val(),
        //         'content': $('#content').val(),
        // },

        success: function (data) {
                if(data.message == "success"){
                    
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
</script>
@endsection
                
                