<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png"  href="{{settingValue('mini_logo') ? settingValue('mini_logo') : asset('backend/Logo.jpg') }}">
    <title>{{ settingValue('application_name') ? settingValue('application_name') : config('app.name', 'Taxi Appz') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />


    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/global_assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/layout.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/colors.min.css') }}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('backend/global_assets/js/main/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/loaders/blockui.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/ui/ripple.min.js') }}"></script>
    <!-- /core JS files -->

    <script src="{{ asset('backend/global_assets/js/plugins/forms/wizards/steps.min.js') }}"></script>
	<!-- <script src="{{ asset('backend/global_assets/js/plugins/forms/selects/select2.min.js') }}"></script> -->
	<script src="{{ asset('backend/global_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
	<script src="{{ asset('backend/global_assets/js/plugins/forms/inputs/inputmask.js') }}"></script>
	<script src="{{ asset('backend/global_assets/js/plugins/forms/validation/validate.min.js') }}"></script>
	<script src="{{ asset('backend/global_assets/js/plugins/extensions/cookie.js') }}"></script>
	<script src="{{ asset('backend/global_assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
	<script src="{{ asset('backend/global_assets/js/demo_pages/form_multiselect.js') }}"></script>
	<script src="{{ asset('backend/global_assets/js/plugins/notifications/pnotify.min.js') }}"></script>
    
    <!-- Theme JS files -->

    <script src="{{ asset('backend/assets/js/app.js') }}"></script>
    <!-- <script src="{{ asset('backend/global_assets/js/demo_pages/login.js') }}"></script> -->
    <!-- /theme JS files -->

    <!-- Theme JS files -->

    <!-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> -->
    <script src="{{ asset('backend/global_assets/js/demo_charts/google/light/lines/lines.js') }}"></script>

    <script src="{{ asset('backend/global_assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/visualization/d3/d3_tooltip.js') }}"></script>
    <!-- <script src="{{ asset('backend/global_assets/js/plugins/forms/styling/switchery.min.js') }}"></script> -->
    <script src="{{ asset('backend/global_assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>

    <script src="{{ asset('backend/global_assets/js/demo_pages/dashboard.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/streamgraph.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/sparklines.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/lines.js') }}"></script>    
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/areas.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/donuts.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/bars.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/progress.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/heatmaps.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/pies.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/bullets.js') }}"></script>


    <script src="{{ asset('main.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/u/ju/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.12,af-2.1.2,b-1.2.0,b-colvis-1.2.0,b-flash-1.2.0,b-html5-1.2.0,b-print-1.2.0,r-2.1.0,rr-1.1.2,sc-1.4.2,se-1.2.0/datatables.min.js"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/tables/datatables/extensions/select.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js') }}"></script>
    <!-- <script src="{{ asset('backend/global_assets/js/demo_pages/datatables_extension_buttons_html5.js') }}"></script> -->
    
    
    <script src="{{ asset('backend/global_assets/js/demo_pages/form_checkboxes_radios.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- /theme JS files -->

	<script src="{{ asset('backend/global_assets/js/demo_pages/form_wizard.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_pages/datatables_extension_buttons_print.js') }}"></script>
    

</head>

<body id="reloadDiv">
    <!-- Main navbar -->
    <div class="navbar navbar-expand-md navbar-dark  navbar-static" style="background-color:#FFD60B">
        <div class="navbar-brand">
            <a href="" class="d-inline-block">
                <!-- <h3 style="color: #fff;">Taxiapp3.0</h3> -->
                <img src="{{settingValue('logo') ? settingValue('logo') : asset('backend/global_assets/images/logo_light.png') }}" alt="">
            </a>
        </div>

        <div class="d-md-none">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
                <i class="icon-tree5"></i>
            </button>
            <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
                <i class="icon-paragraph-justify3"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbar-mobile">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block" style="color:#000000">
                        <i class="icon-paragraph-justify3"></i>
                    </a>
                </li>
            </ul>

            <span class="navbar-text ml-md-3" style="color:#000000">
                <span class="badge badge-mark border-success-300 mr-2"></span>

                Welcome,{{ auth()->user()->firstname }} !!!
            </span>

            <ul class="navbar-nav ml-md-auto">
                <li class="nav-item">
                    <form class="navbar-nav-link" method="POST" action="{{ route('logout') }}">
                        @csrf
                        
                        <button type="submit" class="btn" ><i class="icon-switch2"></i></button>
                    </form>
                    <span class="d-md-none ml-2">Logout</span>
                </li>


            </ul>
        </div>
    </div>
    <!-- /main navbar -->


    <!-- Page content -->
    <div class="page-content">

        <!-- Main sidebar -->
        <div class="sidebar sidebar-light sidebar-main sidebar-expand-md">

            @include('layouts.sidebar')
            
        </div>
        <!-- /main sidebar -->


        <!-- Main content -->
        <div class="content-wrapper">

            <!--content-->
            @yield('content')


            @include('layouts.footer')

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</body>


</html>