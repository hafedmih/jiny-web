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
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">{{ __('reference') }}  </span> </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-lg-7">
                <div class="card">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                            <th>{{ __('s.no') }}</th>
                            <th>{{ __('vehicle_name') }}</th>
                            <th>{{ __('capacity') }}</th>
                            <th>{{ __('count') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicleList as $key => $model)
                           
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{!! $model->vehicle_name!!}</td>    
                                    <td>{!! $model->capacity!!}</td>
                                    <td>{!! $vehicle_model_count[$key]!!}</td>
                                </tr>
                            @endforeach
                    
                        </tbody>
                    </table>
                </div>
        </div>
        <div class=" col-lg-5">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">{{ __('available_vehicles') }}</h5>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div class="chart has-fixed-height" id="available_vehicles"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">

    <!-- Basic columns -->
        <div class="row">
            <div class=" col-lg-4">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title">{{ __('vehicle_trips') }} </h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <select id="vehicle_trips_type" class="form-control" name="today_trips_type">
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
                       
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->

<script>
    var pie_donut_element = document.getElementById('pie_donut');
    var line_stacked_element = document.getElementById('line_stacked');
    var line_vehicle_element = document.getElementById('available_vehicles');
    pie_donut_element_fun(pie_donut_element,"{{$vehicles['auto']}}","{{$vehicles['mini']}}","{{$vehicles['sedan']}}","{{$vehicles['suv']}}","Auto,Mini,Sedan,SUV");
    line_vehicle_element_fun(line_vehicle_element,"{{implode(',',$vehicle)}}","{{implode(',',$available_vehicles)}}");
   

    function pie_donut_element_fun(pie_donut_element,auto,mini,sedan,suv,names){
        if (pie_donut_element) {

            names = names.split(",");

            // Initialize chart
            var pie_donut = echarts.init(pie_donut_element);

            // Options
            pie_donut.setOption({

                // Colors
                color: [
                    '#ebcd63','#c6718c','#ac81f1','#92c906','#44b3cb','#05965a','#3f6fd8'
                ],

                // Add tooltip
                tooltip: {
                    trigger: 'item',
                    backgroundColor: 'rgba(0,0,0,0.75)',
                    padding: [10, 15],
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
                        {value: auto, name: 'Auto'},
                        {value: mini, name: 'Mini'},
                        {value: sedan, name: 'Sedan'},
                        {value: suv, name: 'SUV'}
                    ]
                }]
            });
        }
    }

    function line_vehicle_element_fun(line_vehicle_element,vehicle,available_vehicles,names){
        if (line_vehicle_element) {

            // Initialize chart
            var columns_basic = echarts.init(line_vehicle_element);
            vehicle = vehicle.split(',');
            available_vehicles = available_vehicles.split(',');
            
            var datas = [];
            jQuery.each( available_vehicles, function( i, val ) {
                datas[i] = {
                        name: val,
                        type: 'bar',
                        data: [vehicle[i]],
                        itemStyle: {
                            normal: {
                                label: {
                                    show: true,
                                    position: 'top',
                                    textStyle: {
                                        fontWeight: 500
                                    }
                                }
                            }
                        }
                    }
            });
            
            columns_basic.setOption({

                // Define colors
                color: ['#ebcd63','#ac81f1','#92c906','#c6718c','#44b3cb','#05965a','#3f6fd8'],

                // Global text styles
                textStyle: {
                    fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                    fontSize: 13
                },

                // Chart animation duration
                animationDuration: 1000,

                // Setup grid
                grid: {
                    left: 100,
                    right: 20,
                    top: 35,
                    bottom: 0,
                    containLabel: true
                },

                // Add legend
                // legend: {
                //     data: names,
                //     itemHeight: 8,
                //     itemGap: 20,
                //     textStyle: {
                //         padding: [0, 5]
                //     }
                // },
                legend: {
                    orient: 'vertical',
                    top: 5,
                    left: 0,
                    data: names,
                    itemHeight: 8,
                    itemWidth: 8
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
                    data: ['category'],
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
                series: datas
            });
        }
    }
</script>

@endsection
