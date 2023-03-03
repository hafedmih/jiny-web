@extends('layouts.app')
@section('content')

<style>
    .rounded{
        width: 150px;
            height: 150px;
            position: relative;
            overflow: hidden;
            border-radius: 100px;
    }
    .gradient-custom{
        background: #f6d365;
        background: -webkit-linear-gradient(to right bottom, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1));
        background: linear-gradient(to right bottom, rgba(246, 211, 101, 1), rgba(253, 160, 133, 1))
    }
    div p{
        font-size: 18px;
    }
</style>
<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{__('profile_management')}}</h5>
            <div class="header-elements">
                    <div class="list-icons">
                    <a href="#" onclick="Javascript: return changePassword(`{{$user->slug }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Change Password </a>  
                    </div> 
                    <div class="list-icons">
                    <a href="#" onclick="Javascript: return editAction(`{{ route('profileedit',$user->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                    </div>
            </div>
        </div>
    </div>

        <div class="card mb-3" style="border-radius: .5rem;">
            <div class="row g-0">
                <div class=" image-cropper   col-md-4 gradient-custom text-center text-white" style="border-top-left-radius: 1rem; border-bottom-left-radius: 1rem;">
                    <img 
                        src="@if($user->profile_pic != '') 
                               {{$user->profile_pic}} 
                             @else 
                               {{ asset('backend/global_assets/images/demo/users/face7.jpg') }} 
                             @endif"
                        alt="Avatar"
                        class=" rounded-circle rounded img-fluid my-5"
                        style="width: 150px,height:200px;"
                    />
                    <h5><span style="color:black">{{ $user->firstname }} {{ $user->lastname }}</span> </h5>
                </div>
                <div class="col-md-8">
                    <div class="card-body p-4">
                        <h6>Information</h6>
                        <hr class="mt-0 mb-4">
                        <div class="row">
                            <div class="col-3 mb-3">
                                    <h6>First Name</h6>
                            </div>       
                            <div class="col-3 mb-3">
                                 <p class="text-muted">{{ $user->firstname }}</p>
                            </div>                                                
                        </div>
                        <div class="row">
                            <div class="col-3 mb-3">
                                    <h6>Last Name</h6>
                            </div>       
                            <div class="col-3 mb-3">
                                 <p class="text-muted">{{ $user->lastname }}</p>
                            </div>                                                
                        </div>
                        <div class="row">
                            <div class="col-3 mb-3">
                                    <h6>Language</h6>
                            </div>       
                            <div class="col-3 mb-3">
                               <p class="text-muted"> @if ($user->language == "en")
                                        English
                                    @elseif ($user->language == "ta")
                                        Tamil
                                    @endif</p>
                            </div>                                                
                        </div>
                        <div class="row">
                            <div class="col-3 mb-3">
                                    <h6>Gender</h6>
                            </div>       
                            <div class="col-3 mb-3">
                                 <p class="text-muted">{{ $user->gender }}</p>
                            </div>                                                
                        </div>
                        <div class="row">
                            <div class="col-3 mb-3">
                                    <h6>Phone Number</h6>
                            </div>       
                            <div class="col-3 mb-3">
                                 <p class="text-muted">{{ $user->phone_number }}</p>
                            </div>                                                
                        </div>
                        <div class="row">
                            <div class="col-3 mb-3">
                                    <h6>Email</h6>
                            </div>       
                            <div class="col-3 mb-3">
                                 <p class="text-muted">{{ $user->email }}</p>
                            </div>                                                
                        </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>

    
    <!-- horizontal form -->
    <div id="roleModel1" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading1">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm1" name="roleForm1" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger" id="errorbox1">
                            <button type="button" class="close"><span>×</span></button>
                            <span id="errorContent1"></span>
                        </div>
                        <div class="form-group row password">
                            <label class="col-form-label col-sm-3">{{ __('password') }}</label>
                            <div class="col-sm-9">
                                <input type="password" placeholder="{{ __('password') }}" id="password" class="form-control" name="password">
                                <input type="hidden" name="user_slug" id="user_slug">
                            </div>
                        </div>
                        <div class="form-group row password">
                            <label class="col-form-label col-sm-3">{{ __('confirm-password') }}</label>
                            <div class="col-sm-9">
                                <input type="password" placeholder="{{ __('confirm-password') }}" id="cpassword" class="form-control" name="cpassword">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn1" class="btn bg-primary">{{ __('save-changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                                <label class="col-form-label col-sm-3 ">{{ __('first name') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('first name') }}" id="firstname" class="form-control" name="firstname">
                                    <input type="hidden" name="user_id" id="user_id">
                                </div>
                            </div>
                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3 ">{{ __('lastname') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ __('last name') }}" id="lastname" class="form-control" name="lastname">
                                </div>
                            </div>
                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3">{{ __('language') }}</label>
                                <div class="col-lg-9">
                                <select class="form-control" name="language" id="language">
			                        <option value="">{{ __('language') }}</option>
			                        <option value="en">English</option>
                                    <option value="ta">Tamil </option>                                    
		                        </select>
                                </div>
                            </div>
                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3">{{ __('phonenumber') }}</label>
                                <div class="col-lg-9">
                                     <input type="text" placeholder="{{ __('Phone number') }}" id="phone_number" class="form-control" name="phone_number">
                                </div>
                            </div>
                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3">{{ __('profile_pic') }}</label>
                                <div class="col-sm-9">
                                    <input type="file" id="profile_pic" class="form-control" name="profile_pic">

                                </div>
                            </div>
                            <div class="form-group row required">
                                <label class="col-form-label col-sm-3">{{ __('gender') }}</label>
                                <div class="col-lg-9">
                                <select class="form-control" name="gender" id="gender">
			                        <option value="">{{ __('gender') }}</option>
			                        <option value="Male">Male</option>
                                    <option value="Female">Female </option>
		                        </select>
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
                $('#modelHeading').html("{{ __('edit-profile') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit_user");
                $('#roleModel').modal('show');
                $('#firstname').val(data.user.firstname);
                $('#lastname').val(data.user.lastname);
                $('#language').val(data.user.language);
                $('#gender').val(data.user.gender);
                $('#phone_number').val(data.user.phone_number);
                $('#user_id').val(data.user.slug);
                $('#view_image').attr('src',data.user.profile_pic);
                $('#view_image').show();
                
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }
    function changePassword(actionUrl){
        $('#modelHeading1').html("{{ __('password-change') }}");
        $('#errorbox1').hide();
        $('#roleModel1').modal('show');
        $('#user_slug').val(actionUrl);
        return false;
    }

  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $('#profile_pic').change(function(){
        $("#view_image").show();
          let reader = new FileReader();
          reader.onload = (e) => { 
            $("#view_image").attr('src',e.target.result);
          }
          reader.readAsDataURL(this.files[0]); 
    });


    // $('#add_new_btn').click(function () {
        
    //     $('#roleForm').trigger("reset");
    //     $('#modelHeading').html("{{ __('create-new-user') }}");
    //     $('#roleModel').modal('show');
    //     $('#saveBtn').val("add_user");
    //     $('#errorbox').hide();
    // });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('profile_pic',$('#profile_pic').prop('files').length > 0 ? $('#profile_pic').prop('files')[0] : '');
        formData.append('firstname',$('#firstname').val());
        formData.append('lastname',$('#lastname').val());
        formData.append('language',$('#language').val());
        formData.append('gender',$('#gender').val());
        formData.append('phone_number',$('#phone_number').val());
        formData.append('user_id',$('#user_id').val());
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
        console.log()
        if(btnVal == 'edit_user'){
            $.ajax({
                data: formData,
                url: "{{ route('profileupdate') }}",
                type: "POST",
                dataType: 'json',
                contentType : false,
                processData: false,
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
        }
        
    });

    $('#saveBtn1').click(function (e) {
    e.preventDefault();
    $(this).html("{{ __('sending') }}");
    $('#errorbox1').hide();
   
    $.ajax({
        data: $('#roleForm1').serialize(),
        url: "{{ route('passwordchanging') }}",
        type: "POST",
        dataType: 'json',
        success: function (data) {
                $('#roleForm1').trigger("reset");
                $('#roleModel1').modal('hide');
                swal({
                    title: "{{ __('data-updated') }}",
                    text: "{{ __('data-updated-successfully') }}",
                    icon: "success",
                }).then((value) => {
                    // $("#reloadDiv").load("{{ route('user') }}");
                    location.reload();
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox1').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err);
                $('#errorContent1').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent1').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn1').html("{{ __('save-changes') }}");
            }
    });
    
});

    $(".close").click(function(){
        $('#errorbox').hide();
    })
    

  });
</script>


<!--Profile Photo view-->

 <script>
    var mimes = {
        'jpeg': 'data:image/jpeg;base64,'
    };

         AWS.config.update({
            signatureVersion: 'v4',
            region: '{{settingValue('s3_bucket_default_region')}}',
            accessKeyId: '{{settingValue('s3_bucket_key')}}',
            secretAccessKey: '{{settingValue('s3_bucket_secret_access_key')}}'
        });

        var bucket = new AWS.S3({params: {Bucket: '{{settingValue('s3_bucket_name')}}'}});


      function encode(data)
      {
          var str = data.reduce(function(a,b){ return a+String.fromCharCode(b) },'');
          return btoa(str).replace(/.{76}(?=.)/g,'$&\n');
      }

      function getUrlByFileName(fileName,mimeType) {
          return new Promise(
              function (resolve, reject) {
                  bucket.getObject({Key: fileName}, function (err, file) {
                      var result =  mimeType + encode(file.Body);
                      resolve(result)
                  });
              }
          );
         
      }

      function openInNewTab(url) {
          var redirectWindow = window.open(url, '_blank');
          redirectWindow.location;
      }

        if("{{$user->profile_pic}}" != ""){

          getUrlByFileName('{{$user->profile_pic}}', mimes.jpeg).then(function(data) {
    
        $("#{{$user->id}}").attr('src',data);
      });
      }
   

  </script>

@endsection