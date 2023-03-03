<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png"  href="{{settingValue('mini_logo') ? settingValue('mini_logo') : asset('backend/assets/images/favi-icon-96x96.png') }}">
    <title>{{settingValue('application_name') ? settingValue('application_name') : config('app.name', 'Taxi Appz') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />


    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/global_assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/layout.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/colors.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('backend/global_assets/js/demo_pages/animations_css3.js') }}"></script>

    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('backend/global_assets/js/main/jquery.min.js') }}"></script>
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
    <!-- <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/progress.js') }}"></script> -->
    <!-- <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/heatmaps.js') }}"></script> -->
    <!-- <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/pies.js') }}"></script> -->
    <script src="{{ asset('backend/global_assets/js/demo_charts/pages/dashboard/light/bullets.js') }}"></script>


    <script src="{{ asset('main.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <!-- <script src="https://cdn.datatables.net/u/ju/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.12,af-2.1.2,b-1.2.0,b-colvis-1.2.0,b-flash-1.2.0,b-html5-1.2.0,b-print-1.2.0,r-2.1.0,rr-1.1.2,sc-1.4.2,se-1.2.0/datatables.min.js"></script> -->
    <script src="{{ asset('backend/global_assets/js/plugins/tables/datatables/extensions/select.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js') }}"></script>
    <!-- <script src="{{ asset('backend/global_assets/js/demo_pages/datatables_extension_buttons_html5.js') }}"></script> -->
    
    
    <script src="{{ asset('backend/global_assets/js/demo_pages/form_checkboxes_radios.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- /theme JS files -->

	<script src="{{ asset('backend/global_assets/js/demo_pages/form_wizard.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/demo_pages/datatables_extension_buttons_print.js') }}"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>

    
    <!-- modifyed -->
    <!-- Global stylesheets -->
	

	<link href="{{ asset('backend/global_assets/css/extras/animate.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('backend/global_assets/js/demo_pages/animations_css3.js')}}"></script>
	<!-- /global stylesheets -->
    <script src="{{ asset('backend/global_assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
	<script src="{{ asset('backend/global_assets/js/plugins/visualization/c3/c3.min.js') }}"></script>
    <script src="{{ asset('backend/global_assets/js/plugins/visualization/echarts/echarts.min.js') }}"></script>
	<script src="{{ asset('backend/global_assets/js/plugins/visualization/d3/d3_tooltip.js') }}"></script>
    <!-- asterrisk (*) -->
    
    <script src="{{ asset('backend/global_assets/js/demo_charts/echarts/light/bars/columns_basic.js')}}"></script>
  

  <script>
    function initTheme() {
      const darkThemeSelected =
        localStorage.getItem('darkSwitch') !== null &&
        localStorage.getItem('darkSwitch') === 'dark';
      darkSwitch.checked = darkThemeSelected;
      darkThemeSelected ? document.body.setAttribute('data-theme', 'dark') :
        document.body.removeAttribute('data-theme');
    }

    function resetTheme() {
      if (darkSwitch.checked) {
        document.body.setAttribute('data-theme', 'dark');
        localStorage.setItem('darkSwitch', 'dark');
      } else {
        document.body.removeAttribute('data-theme');
        localStorage.removeItem('darkSwitch');
      }
    }
  </script>

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
                    <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block" style="color:#fff">
                        <i class="icon-paragraph-justify3"></i>
                    </a>
                </li>
            </ul>

            <span class="navbar-text ml-md-3" style="color:#fff">
                <span class="badge badge-mark border-success-300 mr-2"></span>

                Welcome,{{ auth()->user()->firstname }} !!!
            </span>
            <ul class="navbar-nav ml-md-auto">
                
            <li class="nav-item dropdown"  style="padding-top: 10px;">
					<span href="#" class="navbar-nav-link dropdown-toggle nofication" data-toggle="dropdown">
						<i class="icon-bubble-notification	font-size-base mr-2" style="font-size:16px;"></i>
						Notification
					</span>
					
					<div class="dropdown-menu dropdown-menu-right dropdown-content wmin-md-350">
						<div class="dropdown-content-header bg-light"  style="border-top:1px solid #ddd;">
							<span class="font-size-sm line-height-sm text-uppercase font-weight-semibold">Latest Notification</span>
						</div>

						<div class="dropdown-content-body dropdown-scrollable" id="test">
					
						</div>

						<div class="dropdown-content-footer bg-light">
							<a href="{{ route('documentExpiry') }}" class="font-size-sm line-height-sm text-uppercase font-weight-semibold text-grey mr-auto">View All document expiry</a>
						</div>
					</div>
			</li>
                
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

<script>

$(document).on('click','.nofication',function(){
        var values = $(this).val();
        var texts = '';
        $.ajax({
            url: "{{ url('notify') }}",
            type: "GET",
            dataType: 'json',
            success: function (data) {
              data = data.data;
                    data.forEach(element => {
                        texts += `<ul class="media-list"><li class="media"><div class="mr-3"><a href="#" class="btn bg-success-400 rounded-round btn-icon"><i class="icon-mention"></i></a></div><div class="media-body">${element.firstname} ${element.lastname} -  ${element.document_name} is Expired with in ${element.days} Days<div class="font-size-sm text-muted mt-1"></div></div></li></ul>`;
                    });
                $("#test").html(texts);
            },
            
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                //console.log(err.errors);
                texts += `<ul class="media-list"><li class="media"><div class="mr-3"><a href="#" class="btn bg-success-400 rounded-round btn-icon"><i class="icon-mention"></i></a></div><div class="media-body">No Document Expiry Found !<div class="font-size-sm text-muted mt-1"></div></div></li></ul>`;
                $('#test').html(texts);
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    })


    var message = "{{session()->get('message')}}";
    var status = "{{session()->get('status')}}";

    if(message && status == true){
        swal({
            title: message,
            text: "{{ __('successfully') }}",
            icon: "success",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

    if(message && status == false){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }
</script>

