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

</head>

<body class="bg-slate-800">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">
            
            <!-- Content area -->
            <div class="content d-flex justify-content-center align-items-center">
            
                <!-- Login card -->
                <form class="login-form" method="POST" action="{{ route('2fa.post') }}">
                    @csrf
                    
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="icon-shield-check icon-2x text-primary-600 border-primary-600 border-3 rounded-round p-3 mb-3 mt-1"></i>
                                <h5 class="mb-0">{{ __('auth.two_factor_authentication') }}</h5>
                                <span class="d-block text-muted">{{ __('auth.your-credentials') }}</span>
                            </div>

                            <p class="text-center">We've sent a code to  : {{ substr(auth()->user()->email, 0, 5) . '******' . substr(auth()->user()->email,  -4) }}</p>
                
                            @if ($message = Session::get('success'))
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-success alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button> 
                                        <strong>{{ $message }}</strong>
                                    </div>
                                </div>
                                </div>
                            @endif

                            @if ($message = Session::get('error'))
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button> 
                                        <strong>{{ $message }}</strong>
                                    </div>
                                </div>
                                </div>
                            @endif

                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Code</label>

                                <div class="col-md-6">
                                    <input id="code" type="number" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" required autocomplete="code" autofocus>

                                    @error('code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group d-flex align-items-center">
                            <div class="form-group row ">
                                <div class="col-md-12 ">
                                    <a class="btn btn-link" href="{{ route('2fa.resend') }}">Resend Code?</a>
                                </div>
                            </div>
                            

                                <!-- <a href="" class="ml-auto">{{ __('Forgot your password?') }}</a> -->
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                        
                </form>
                <!-- /login card -->
                <!-- <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">2FA Verification</div>
                
                                <div class="card-body">
                                    <form method="POST" action="{{ route('2fa.post') }}">
                                        @csrf
                
                                        <p class="text-center">We sent code to email : {{ substr(auth()->user()->email, 0, 5) . '******' . substr(auth()->user()->email,  -2) }}</p>
                
                                        @if ($message = Session::get('success'))
                                            <div class="row">
                                            <div class="col-md-12">
                                                <div class="alert alert-success alert-block">
                                                    <button type="button" class="close" data-dismiss="alert">×</button> 
                                                    <strong>{{ $message }}</strong>
                                                </div>
                                            </div>
                                            </div>
                                        @endif
                
                                        @if ($message = Session::get('error'))
                                            <div class="row">
                                            <div class="col-md-12">
                                                <div class="alert alert-danger alert-block">
                                                    <button type="button" class="close" data-dismiss="alert">×</button> 
                                                    <strong>{{ $message }}</strong>
                                                </div>
                                            </div>
                                            </div>
                                        @endif
                
                                        <div class="form-group row">
                                            <label for="code" class="col-md-4 col-form-label text-md-right">Code</label>
                
                                            <div class="col-md-6">
                                                <input id="code" type="number" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" required autocomplete="code" autofocus>
                
                                                @error('code')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                
                                        <div class="form-group row mb-0">
                                            <div class="col-md-8 offset-md-4">
                                                <a class="btn btn-link" href="{{ route('2fa.resend') }}">Resend Code?</a>
                                            </div>
                                        </div>
                
                                        <div class="form-group row mb-0">
                                            <div class="col-md-8 offset-md-4">
                                                <button type="submit" class="btn btn-primary">
                                                    Submit
                                                </button>
                
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</body>
</html>


