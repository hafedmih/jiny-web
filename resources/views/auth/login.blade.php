<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png"  href="{{settingValue('mini_logo') ? settingValue('mini_logo') : asset('backend/Logo.jpg') }}">    <title>{{settingValue('application_name') ? settingValue('application_name') : config('app.name', 'Taxi Appz') }}</title>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/global_assets/css/icons/icomoon/styles.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/layout.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/colors.min.css') }}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('backend/global_assets/js/main/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/main/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/loaders/blockui.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/ui/ripple.min.js') }}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="{{ asset('backend/global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script src="{{ asset('backend/assets/js/app.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_pages/login.js') }}"></script>
    <!-- /theme JS files -->
    <style>
       .icon-eye-blocked {
        float: right;
        margin-left: -25px;
        margin-top: -25px;
        position: relative;
        z-index: 2;
        }

        .container{
        padding-top:50px;
        margin: auto;
        }
    </style>

</head>

<body class="bg-slate-800">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">
            
            <!-- Content area -->
            <div class="content d-flex justify-content-center align-items-center">
            
                <!-- Login card -->
                <form class="login-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="icon-lock5 icon-2x text-primary-600 border-primary-600 border-3 rounded-round p-3 mb-3 mt-1"></i>
                                <h5 class="mb-0">{{ __('auth.login-into-account') }}</h5>
                                <span class="d-block text-muted">{{ __('auth.your-credentials') }}</span>
                            </div>

                            <div class="form-group form-group-feedback form-group-feedback-left">
                                <input type="email" name="email" class="form-control" placeholder="Username">
                                <div class="form-control-feedback">
                                    <i class="icon-user text-muted"></i>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="form-text text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="form-group form-group-feedback form-group-feedback-left">
                                <input type="password" name="password" id="password-field" class="form-control" placeholder="Password">
                                <div class="form-control-feedback">
                                    <i class="icon-lock2 text-muted"></i>
                                </div>
                                <div class=" form-group-feedback-right">
                                <span toggle="#password-field" class="icon-eye-blocked toggle-password"></span>        
								</div>

                                
                                @if ($errors->has('password'))
                                    <span class="form-text text-danger">{{ $errors->first('password') }}</span>
                                @endif
                               
                            </div>
                            
                           

                            <div class="form-group d-flex align-items-center">
                                <div class="form-check mb-0">
                                    <label class="form-check-label">
                                        <input type="checkbox" id="remember_me" name="remember" class="form-input-styled" checked data-fouc>
                                        Remember
                                    </label>
                                </div>

                                <!-- <a href="" class="ml-auto">{{ __('Forgot your password?') }}</a> -->
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">{{ __('Log in') }}<i class="icon-circle-right2 ml-2"></i></button>
                            </div>

                        </div>
                    </div>
                </form>
                <!-- /login card -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</body>

<script>
$(".toggle-password").click(function() {

$(this).toggleClass("icon-eye");
var input = $($(this).attr("toggle"));
if (input.attr("type") == "password") {
  input.attr("type", "text");
} else {
  input.attr("type", "password");
}
});
</script>
</html>




