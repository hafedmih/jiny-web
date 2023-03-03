@extends('layouts.app')
<style>
    .chart-container-height{
        margin-top: -89px;
    }
    .height{
        margin-top: -81px;
    }
</style>
@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">

<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">{{ __('dashboard') }}  </span> </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class=" col-lg-4">
            <div class="card">
				<div class="card-header header-elements-inline">
					<h5 class="card-title">{{ __('today_trips') }} </h5>
					<div class="header-elements">
						<div class="list-icons">
				            <!-- <a class="list-icons-item" data-action="collapse"></a>
				            <a class="list-icons-item" data-action="reload"></a>
				            <a class="list-icons-item" data-action="remove"></a> -->
                            <select id="today_trips_type" class="form-control" name="today_trips_type">
			                    <option value="1">{{ __('today') }}</option>
			                    <option value="2">{{ __('this_week') }}</option>
			                    <option value="3">{{ __('last_week') }}</option>
			                    <option value="4">{{ __('this_month') }}</option>
			                    <option value="5">{{ __('last_month') }}</option>
			                </select>
				        </div>
			        </div>
				</div>

				<div class="card-body row">
                    <div class="col-lg-12" style="height:350px;overflow: hidden;" >
						<div class="chart-container-height ">
							<div class="chart has-fixed-height " id="pie_donut"></div>
					    </div>
                        <br><br>
                        <div class="row text-center height" >
							<div class="col-3">
								<div class="mb-2">
									<h3 class="font-weight-bold mb-0 trips_total">{{$trips['total']}}</h3>
									<span class=" font-weight-semibold font-size-lg">{{ __('total_trip') }}</span>
								</div>
							</div>
							<div class="col-3">
								<div class="mb-2">
									<h3 class="font-weight-bold mb-0 trips_complete">{{$trips['completed']}}</h3>
									<span class=" font-weight-semibold font-size-lg">{{ __('completed_trip') }}</span>
								</div>
							</div>
							<div class="col-3">
								<div class="mb-2">
									<h3 class="font-weight-bold mb-0 trips_cancelled">{{$trips['cancelled']}}</h3>
									<span class=" font-weight-semibold font-size-lg">{{ __('cancelled_trip') }}</span>
								</div>
							</div>
							<div class="col-3">
								<div class="mb-2">
									<h3 class="font-weight-bold mb-0 trips_pending">{{$trips['pending']}}</h3>
									<span class=" font-weight-semibold font-size-lg">{{ __('pending_trip') }}</span>
								</div>
							</div>
						</div>
                    </div>
				</div>
			</div>
        </div>
        <div class="col-lg-8">
            <!-- Stacked columns -->
				<div class="card">
					<div class="card-header header-elements-inline">
						<h5 class="card-title">{{ __('amount_transaction') }}</h5>
						<div class="header-elements">
							<div class="list-icons">
		                		<!-- <a class="list-icons-item" data-action="collapse"></a>
		                		<a class="list-icons-item" data-action="reload"></a>
		                		<a class="list-icons-item" data-action="remove"></a> -->
                                <select id="amount_transaction" class="form-control" name="amount_transaction">
                                    <option value="2">{{ __('this_week') }}</option>
                                    <option value="3">{{ __('last_week') }}</option>
                                </select>
		                	</div>
	                	</div>
					</div>

					<div class="card-body">
						<div class="chart-container">
							<div class="chart has-fixed-height" id="columns_stacked"></div>
						</div>
                        <br><br>
                        <div class="row text-center">
							<div class="col-3">
								<div class="mb-3">
									<h5 class="font-weight-semibold mb-0 total_amount_add">{{$currency}} {{$amount['total_amount_add']}}</h5>
									<span class="text-muted font-size-sm">{{ __('total_amount') }}</span>
								</div>
							</div>
							<div class="col-3">
								<div class="mb-3">
									<h5 class="font-weight-semibold mb-0 admin_amount_add">{{$currency}} {{$amount['admin_amount_add']}}</h5>
									<span class="text-muted font-size-sm">{{ __('admin_commission') }}</span>
								</div>
							</div>
							<div class="col-3">
								<div class="mb-3">
									<h5 class="font-weight-semibold mb-0 driver_amount_add">{{$currency}} {{$amount['driver_amount_add']}}</h5>
									<span class="text-muted font-size-sm">{{ __('driver_commission') }}</span>
								</div>
							</div>
							<div class="col-3">
								<div class="mb-3">
									<h5 class="font-weight-semibold mb-0 tax_amount_add">{{$currency}} {{$amount['tax_amount_add']}}</h5>
									<span class="text-muted font-size-sm">{{ __('service_tax') }}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /stacked columns -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
						<div class="card-body text-center">
							<i class="icon-man icon-2x text-success-400 border-success-400 border-3 rounded-round p-3 mb-3 mt-1"></i>
							<h5 class="card-title">{{$users['users']['total']}}</h5>
							<p class="mb-3">{{ __('users') }}</p>
							<div class="row text-center">
								<div class="col-4">
									<h5 class="font-weight-semibold mb-0">{{$users['users']['total']}}</h5>
									<span class="text-muted font-size-sm">{{ __('total') }}</span>
								</div>
								<div class="col-4">
									<h5 class="font-weight-semibold mb-0">{{$users['users']['active']}}</h5>
									<span class="text-muted font-size-sm">{{ __('active') }}</span>
								</div>
								<div class="col-4">
									<h5 class="font-weight-semibold mb-0">{{$users['users']['block']}}</h5>
									<span class="text-muted font-size-sm">{{ __('block') }}</span>
								</div>
							</div>
						</div>
					</div>
                </div>
                <div class="col-md-4">
                    <div class="card">
						<div class="card-body text-center">
							<i class="icon-users2 icon-2x text-danger-400 border-danger-400 border-3 rounded-round p-3 mb-3 mt-1"></i>
							<h5 class="card-title">{{$users['driver']['total']}}</h5>
							<p class="mb-3">{{ __('drivers') }}</p>
							<div class="row text-center">
								<div class="col-4">
									<h5 class="font-weight-semibold mb-0">{{$users['driver']['total']}}</h5>
									<span class="text-muted font-size-sm">{{ __('total') }}</span>
								</div>
								<div class="col-4">
									<h5 class="font-weight-semibold mb-0">{{$users['driver']['active']}}</h5>
									<span class="text-muted font-size-sm">{{ __('active') }}</span>
								</div>
								<div class="col-4">
									<h5 class="font-weight-semibold mb-0">{{$users['driver']['block']}}</h5>
									<span class="text-muted font-size-sm">{{ __('block') }}</span>
								</div>
							</div>
						</div>
					</div>
                </div>
                @if(auth()->user()->hasRole('Super Admin'))
                <div class="col-md-4">
                    <div class="card">
						<div class="card-body text-center">
							<i class="icon-location4 icon-2x text-warning-400 border-warning-400 border-3 rounded-round p-3 mb-3 mt-1"></i>
							<h5 class="card-title">{{$users['zone']['total']}}</h5>
							<p class="mb-3">{{ __('zone_area') }}</p>
							<div class="row text-center">
								<div class="col-4">
									<h5 class="font-weight-semibold mb-0">{{$users['zone']['total']}}</h5>
									<span class="text-muted font-size-sm">{{ __('total') }}</span>
								</div>
								<div class="col-4">
									<h5 class="font-weight-semibold mb-0">{{$users['zone']['active']}}</h5>
									<span class="text-muted font-size-sm">{{ __('active') }}</span>
								</div>
								<div class="col-4">
									<h5 class="font-weight-semibold mb-0">{{$users['zone']['block']}}</h5>
									<span class="text-muted font-size-sm">{{ __('block') }}</span>
								</div>
							</div>
						</div>
					</div>
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body text-center">
				<h6 class="font-weight-semibold mb-0 mt-1">{{ __('today_amount_transaction') }}</h6>
				<!-- <div class="font-size-sm text-muted mb-3">+24% since 2016</div> -->
                <div class="svg-center" id="donut_basic_stats"></div>
				<div class="row text-center">
					<div class="col-4">
						<div class="mt-3">
                            <i class="icon-wallet icon-2x d-inline-block text-danger"></i>
						    <h5 class="font-weight-semibold mb-0">{{$currency}} {{$amount['today_amount']['wallet']}}</h5>
							<span class="text-muted font-size-sm">{{ __('wallet') }}</span>
						</div>
					</div>
					<div class="col-4">
						<div class="mt-3">
                            <i class="icon-credit-card2 icon-2x d-inline-block text-primary"></i>
							<h5 class="font-weight-semibold mb-0">{{$currency}} {{$amount['today_amount']['card']}}</h5>
							<span class="text-muted font-size-sm">{{ __('card') }}</span>
						</div>
					</div>
					<div class="col-4">
						<div class="mt-3">
                            <i class="icon-cash icon-2x d-inline-block text-success"></i>
							<h5 class="font-weight-semibold mb-0">{{$currency}} {{$amount['today_amount']['cash']}}</h5>
							<span class="text-muted font-size-sm">{{ __('cash') }}</span>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
    <!-- Axis labels -->
    <div class="card">
	    <div class="card-header header-elements-inline">
			<h5 class="card-title">{{ __('zone_trips') }}</h5>
			<div class="header-elements">
				<div class="list-icons">
		            <!-- <a class="list-icons-item" data-action="collapse"></a>
		    		<a class="list-icons-item" data-action="reload"></a>
                	<a class="list-icons-item" data-action="remove"></a> -->
                    <select id="zone_trips" class="form-control" name="zone_trips">
			            <option value="2">{{ __('this_week') }}</option>
		                <option value="3">{{ __('last_week') }}</option>
			            <option value="4">{{ __('this_month') }}</option>
			            <option value="5">{{ __('last_month') }}</option>
			            <option value="6">{{ __('last_2_month') }}</option>
			        </select>
	        	</div>
            </div>
		</div>
        <div class="card-body">
			<div class="chart-container">
				<div class="chart has-fixed-height" id="line_stacked"></div>
			</div>
		</div>
	</div>
	<!-- /axis labels -->
    <div class="row">
        <div class="col-md-6">
            <!-- Axis labels -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">{{ __('trips_cancellation') }}</h5>
                    <div class="header-elements">
                        <div class="list-icons">
                            <!-- <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a> -->
                            <select id="cancel_trips" class="form-control" name="cancel_trips">
                                <option value="2">{{ __('this_week') }}</option>
                                <option value="3">{{ __('last_week') }}</option>
                                <!-- <option value="4">{{ __('this_month') }}</option>
                                <option value="5">{{ __('last_month') }}</option>
                                <option value="6">{{ __('last_2_month') }}</option> -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div class="chart has-fixed-height" id="columns_stacked1"></div>
                    </div>
                </div>
            </div>
            <!-- /axis labels -->
        </div>
    </div>
</div>
<!-- /horizontal form modal -->

<script>

$.ajax({
    url: "https://apis.mapmyindia.com/advancedmaps/api/{{settingValue('google_map_token')}}/map_sdk_plugins",
    type: "GET",
    dataType: 'json',
    success: function (data) {
        console.log("2");
        
    },
    error: function (data) {
        if(data.responseJSON.error_code == 'invalid_token' || data.responseJSON.error_code == 'CLIENT_CREDENTIAL_EXPIRED'){
            $.ajax({
                url: "{{ route('gendrateMapToken') }}",
                type: "GET",
                dataType: 'json',
                success: function (datas) {
                    // location.reload();
                }
            });
        }
    }
    });

    
    var pie_donut_element = document.getElementById('pie_donut');
    var columns_stacked_element = document.getElementById('columns_stacked');
    var columns_stacked_element_1 = document.getElementById('columns_stacked1');
    var line_stacked_element = document.getElementById('line_stacked');

    line_stacked_element_fun(line_stacked_element,"{{implode(',',$zone['dates'])}}","{{implode(',',$zone['zone_name'])}}","{{$zone['data']}}");
    pie_donut_element_fun(pie_donut_element,"{{$trips['completed']}}","{{$trips['cancelled']}}","{{$trips['pending']}}","Completed,Cancelled,Pending");
    columns_stacked_element_fun(columns_stacked_element,"{{implode(',',$amount['week_days'])}}","{{implode(',',$amount['total_amount'])}}","{{implode(',',$amount['admin_amount'])}}","{{implode(',',$amount['driver_amount'])}}","{{implode(',',$amount['tax_amount'])}}","","","Total,Admin,Driver,Service Tax");
    columns_stacked_element_fun(columns_stacked_element_1,"{{implode(',',$cancellation['week_days'])}}","{{implode(',',$cancellation['total'])}}","{{implode(',',$cancellation['user'])}}","{{implode(',',$cancellation['driver'])}}","{{implode(',',$cancellation['dispatcher'])}}","","{{implode(',',$cancellation['automatic'])}}","{{implode(',',$cancellation['option'])}}");

    $(document).on('change','#today_trips_type',function(){
        var values = $(this).val();
        $.ajax({
            url: "{{ url('dashboard-total-trips') }}/"+values,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                data = data.data;
                pie_donut_element_fun(pie_donut_element,data.completed,data.cancelled,data.pending,"Completed,Cancelled,Pending");
                $(".trips_total").text(data.total);
                $(".trips_complete").text(data.completed);
                $(".trips_cancelled").text(data.cancelled);
                $(".trips_pending").text(data.pending);

            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.errors);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    })

    $(document).on('change','#amount_transaction',function(){
        var values = $(this).val();
        $.ajax({
            url: "{{ url('dashboard-amount-transaction') }}/"+values,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                data = data.data;
                columns_stacked_element_fun(columns_stacked_element,data.week_days,data.total_amount,data.admin_amount,data.driver_amount,data.tax_amount,'','',"Total,Admin,Driver,service Tax");
                $(".admin_amount_add").text(data.admin_amount_add);
                $(".driver_amount_add").text(data.driver_amount_add);
                $(".total_amount_add").text(data.total_amount_add);
                $(".tax_amount_add").text(data.tax_amount_add);
                // $(".trips_pending").text(data.pending);

            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.errors);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    })

    $(document).on('change','#zone_trips',function(){
        var values = $(this).val();
        $.ajax({
            url: "{{ url('dashboard-zone-trips') }}/"+values,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                data = data.data;
                line_stacked_element_fun(line_stacked_element,data.dates,data.zone_name,data.data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.errors);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    })

    $(document).on('change','#cancel_trips',function(){
        var values = $(this).val();
        $.ajax({
            url: "{{ url('dashboard-cancel-trips') }}/"+values,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                data = data.data;
                console.log(data);
                columns_stacked_element_fun(columns_stacked_element_1,data.week_days,data.total,data.user,data.driver,data.dispatcher,data.automatic,data.option);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.errors);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    })

    function line_stacked_element_fun(line_stacked_element,weeks,zone_name,datas){
        // Axis labels
        if (line_stacked_element) {

            // Initialize chart
            var line_stacked = echarts.init(line_stacked_element);
            weeks = weeks.split(",");
            var zone_data = [];
            zone_name = zone_name.split(",");
            datas = datas.split("|");

            jQuery.each( zone_name, function( i, val ) {
                zone_data[i] = {
                        name: val,
                        type: 'line',
                        smooth: true,
                        symbolSize: 6,
                        itemStyle: {
                            normal: {
                                borderWidth: 2
                            }
                        },
                        data: datas[i].split(",")
                    };
            });
            console.log(zone_data);
            jQuery.each( weeks, function( i, val ) {
                weeks[i] = val.split("-").reverse().join("-");
            });
            // Options
            line_stacked.setOption({

                // Define colors
                color: ["#424956", "#ffd60b", '#0092ff','#d87a80'],

                // Global text styles
                textStyle: {
                    fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                    fontSize: 13
                },

                // Chart animation duration
                animationDuration: 750,

                // Setup grid
                grid: {
                    left: 0,
                    right: 40,
                    top: 35,
                    bottom: 60,
                    containLabel: true
                },

                // Add legend
                legend: {
                    data: zone_name,
                    itemHeight: 8,
                    itemGap: 20
                },

                // Add tooltip
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: 'rgba(0,0,0,0.75)',
                    padding: [10, 15],
                    textStyle: {
                        fontSize: 13,
                        fontFamily: 'Roboto, sans-serif'
                    }
                },

                // Horizontal axis
                xAxis: [{
                    type: 'category',
                    boundaryGap: false,
                    axisLabel: {
                        color: '#333'
                    },
                    axisLine: {
                        lineStyle: {
                            color: '#999'
                        }
                    },
                    data: weeks
                }],

                // Vertical axis
                yAxis: [{
                    type: 'value',
                    axisLabel: {
                        formatter: '{value} ',
                        color: '#333'
                    },
                    axisLine: {
                        lineStyle: {
                            color: '#999'
                        }
                    },
                    splitLine: {
                        lineStyle: {
                            color: ['#eee']
                        }
                    },
                    splitArea: {
                        show: true,
                        areaStyle: {
                            color: ['rgba(250,250,250,0.1)', 'rgba(0,0,0,0.01)']
                        }
                    }
                }],

                // Add series
                series: zone_data
            });
        }
    }

    function pie_donut_element_fun(pie_donut_element,completed,cancelled,pending,names){
        if (pie_donut_element) {

            names = names.split(",");

            // Initialize chart
            var pie_donut = echarts.init(pie_donut_element);

            // Options
            pie_donut.setOption({

                // Colors
                color: [
                    '#34c33a','#f00','#cdcf39'
                ],

                // Add tooltip
                tooltip: {
                    trigger: 'item',
                    backgroundColor: 'rgba(0,0,0,0.75)',
                    padding: [1, 1],
                    textStyle: {
                        fontSize: 13,
                        fontFamily: 'Roboto, sans-serif'
                    },
                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                },

                // Add legend
                legend: {
                    orient: 'vertical',
                    top: 275,
                    left: 0,
                    data: names,
                    itemHeight: 8,
                    itemWidth: 8
                },

                // Add series
                series: [{
                    name: 'Browsers',
                    type: 'pie',
                    radius: ['35%', '50%'],
                    center: ['51%', '51.5%'],
                    itemStyle: {
                        normal: {
                            borderWidth: 1,
                            borderColor: '#fff'
                        }
                    },
                    data: [
                        {value: completed, name: 'Completed'},
                        {value: cancelled, name: 'Cancelled'},
                        {value: pending, name: 'Pending'}
                    ]
                }]
            });
        }
    }

    // Simple donut
    // var _animatedDonut = function(element, size) {
    //     if (typeof d3 == 'undefined') {
    //         console.warn('Warning - d3.min.js is not loaded.');
    //         return;
    //     }

    //     // Initialize chart only if element exsists in the DOM
    //     if(element) {

    //         // Add data set
    //         var data = [
    //             {
    //                 "status": "Wallet",
    //                 "icon": "<i class='icon-wallet text-danger mr-2'></i>",
    //                 "value": {{$amount['today_amount']['wallet']}},
    //                 "color": "#EF5350"
    //             },
    //             {
    //                 "status": "Card",
    //                 "icon": "<i class='icon-credit-card2 text-blue mr-2'></i>",
    //                 "value": {{$amount['today_amount']['card']}},
    //                 "color": "#29B6F6"
    //             }, {
    //                 "status": "Cash",
    //                 "icon": "<i class='icon-cash text-success mr-2'></i>",
    //                 "value": {{$amount['today_amount']['cash']}},
    //                 "color": "#66BB6A"
    //             }
    //         ];

    //         // Main variables
    //         var d3Container = d3.select(element),
    //             distance = 2, // reserve 2px space for mouseover arc moving
    //             radius = (size/2) - distance,
    //             sum = d3.sum(data, function(d) { return d.value.toFixed(2); });


    //         // Tooltip
    //         // ------------------------------

    //         var tip = d3.tip()
    //             .attr('class', 'd3-tip')
    //             .offset([-10, 0])
    //             .direction('e')
    //             .html(function (d) {
    //                 return "<ul class='list-unstyled mb-1'>" +
    //                     "<li>" + "<div class='font-size-base my-1'>" + d.data.icon + d.data.status + "</div>" + "</li>" +
    //                     "<li>" + "Total: &nbsp;" + "<span class='font-weight-semibold float-right'>" + d.value.toFixed(2) + "</span>" + "</li>" +
    //                     "<li>" + "Share: &nbsp;" + "<span class='font-weight-semibold float-right'>" + (100 / (sum / d.value)) + "%" + "</span>" + "</li>" +
    //                 "</ul>";
    //             });


    //         // Create chart
    //         // ------------------------------

    //         // Add svg element
    //         var container = d3Container.append("svg").call(tip);
            
    //         // Add SVG group
    //         var svg = container
    //             .attr("width", size)
    //             .attr("height", size)
    //             .append("g")
    //                 .attr("transform", "translate(" + (size / 2) + "," + (size / 2) + ")");  


    //         // Construct chart layout
    //         // ------------------------------

    //         // Pie
    //         var pie = d3.layout.pie()
    //             .sort(null)
    //             .startAngle(Math.PI)
    //             .endAngle(3 * Math.PI)
    //             .value(function (d) { 
    //                 return d.value;
    //             }); 

    //         // Arc
    //         var arc = d3.svg.arc()
    //             .outerRadius(radius)
    //             .innerRadius(radius / 1.5);


    //         //
    //         // Append chart elements
    //         //

    //         // Group chart elements
    //         var arcGroup = svg.selectAll(".d3-arc")
    //             .data(pie(data))
    //             .enter()
    //             .append("g") 
    //                 .attr("class", "d3-arc d3-slice-border")
    //                 .style({
    //                     'cursor': 'pointer'
    //                 });
            
    //         // Append path
    //         var arcPath = arcGroup
    //             .append("path")
    //             .style("fill", function (d) {
    //                 return d.data.color;
    //             });

    //         // Add tooltip
    //         arcPath
    //             .on('mouseover', function (d, i) {

    //                 // Transition on mouseover
    //                 d3.select(this)
    //                 .transition()
    //                     .duration(500)
    //                     .ease('elastic')
    //                     .attr('transform', function (d) {
    //                         d.midAngle = ((d.endAngle - d.startAngle) / 2) + d.startAngle;
    //                         var x = Math.sin(d.midAngle) * distance;
    //                         var y = -Math.cos(d.midAngle) * distance;
    //                         return 'translate(' + x + ',' + y + ')';
    //                     });
    //             })
    //             .on("mousemove", function (d) {
                    
    //                 // Show tooltip on mousemove
    //                 tip.show(d)
    //                     .style("top", (d3.event.pageY - 40) + "px")
    //                     .style("left", (d3.event.pageX + 30) + "px");
    //             })
    //             .on('mouseout', function (d, i) {

    //                 // Mouseout transition
    //                 d3.select(this)
    //                 .transition()
    //                     .duration(500)
    //                     .ease('bounce')
    //                     .attr('transform', 'translate(0,0)');

    //                 // Hide tooltip
    //                 tip.hide(d);
    //             });

    //         // Animate chart on load
    //         arcPath
    //             .transition()
    //                 .delay(function(d, i) { return i * 500; })
    //                 .duration(500)
    //                 .attrTween("d", function(d) {
    //                     var interpolate = d3.interpolate(d.startAngle,d.endAngle);
    //                     return function(t) {
    //                         d.endAngle = interpolate(t);
    //                         return arc(d);  
    //                     }; 
    //                 });


    //         //
    //         // Append counter
    //         //

    //         // Append text
    //         svg
    //             .append('text')
    //             .attr('class', 'd3-text')
    //             .attr('text-anchor', 'middle')
    //             .attr('dy', 6)
    //             .style({
    //                 'font-size': '17px',
    //                 'font-weight': 500
    //             });

    //         // Animate text
    //         svg.select('text')
    //             .transition()
    //             .duration(1500)
    //             .tween("text", function(d) {
    //                 var i = d3.interpolate(this.textContent, sum);
    //                 return function(t) {
    //                     this.textContent = d3.format(",d")(Math.round(i(t)));
    //                 };
    //             });
    //     }
    // };
    
    _animatedDonut("#donut_basic_stats", 120);

    function columns_stacked_element_fun(columns_stacked_element,week_days,total_amount,admin_amount,driver_amount,tax_amount,dispatcher_amount,automatic_amount,names){
        if (columns_stacked_element) {
            week_days = week_days.split(",");
            total_amount = total_amount.split(",");
            admin_amount = admin_amount.split(",");
            driver_amount = driver_amount.split(",");
            tax_amount = tax_amount.split(",");
            dispatcher_amount = dispatcher_amount.split(",");
            automatic_amount = automatic_amount.split(",");
            names = names.split(",");

            var datas = [];
            var cancel_data = [];
            datas.push(total_amount);
            datas.push(admin_amount);
            datas.push(driver_amount);
            datas.push(tax_amount);
            datas.push(dispatcher_amount);
            datas.push(automatic_amount);

            jQuery.each( names, function( i, val ) {
                if(i > 0){
                    var Advertising = 'Advertising';
                }
                else{
                    var Advertising = '';
                }
                cancel_data[i] = {
                        name: val,
                        type: 'bar',
                        stack: Advertising,
                        data: datas[i]
                    };
            });

            jQuery.each( week_days, function( i, val ) {
                week_days[i] = val.split("-").reverse().join("-");
            });

            // Initialize chart
            var columns_stacked = echarts.init(columns_stacked_element);


            //
            // Chart config
            //

            // Options
            columns_stacked.setOption({

                // Define colors
                color: ['#ffd60b','#000','#5ab1ef','#ffb980','#d87a80'],

                // Global text styles
                textStyle: {
                    fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                    fontSize: 13
                },

                // Chart animation duration
                animationDuration: 750,

                // Setup grid
                grid: {
                    left: 0,
                    right: 10,
                    top: 35,
                    bottom: 0,
                    containLabel: true
                },

                // Add legend
                legend: {
                    data: names,
                    itemHeight: 8,
                    itemGap: 20
                },

                // Add tooltip
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: 'rgba(0,0,0,0.75)',
                    padding: [10, 15],
                    textStyle: {
                        fontSize: 13,
                        fontFamily: 'Roboto, sans-serif'
                    },
                    axisPointer: {
                        type: 'shadow',
                        shadowStyle: {
                            color: 'rgba(0,0,0,0.025)'
                        }
                    }
                },

                // Horizontal axis
                xAxis: [{
                    type: 'category',
                    data: week_days,
                    axisLabel: {
                        color: '#333'
                    },
                    axisLine: {
                        lineStyle: {
                            color: '#999'
                        }
                    },
                    splitLine: {
                        show: true,
                        lineStyle: {
                            color: '#eee',
                            type: 'dashed'
                        }
                    }
                }],

                // Vertical axis
                yAxis: [{
                    type: 'value',
                    axisLabel: {
                        color: '#333'
                    },
                    axisLine: {
                        lineStyle: {
                            color: '#999'
                        }
                    },
                    splitLine: {
                        lineStyle: {
                            color: '#eee'
                        }
                    },
                    splitArea: {
                        show: true,
                        areaStyle: {
                            color: ['rgba(250,250,250,0.1)', 'rgba(0,0,0,0.01)']
                        }
                    }
                }],

                // Add series
                series: cancel_data
            });
        }
    }

    
</script>

@endsection
