@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.179.0.min.js"></script>


<div class="content">

   
 
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Driver Status</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        @if(auth()->user()->can('active-drivers'))
                          <li class="nav-item "><a href="#right-icon-tab1" class="nav-link active" data-toggle="tab"><i class="icon-user-check ml-2 text-success-800"></i><span class="text-success-400"> Online </span></a></li>
                        @endif
                        @if(auth()->user()->can('inactive-drivers'))
                          <li class="nav-item"><a href="#right-icon-tab2" class="nav-link " data-toggle="tab"><i class="icon-user-block ml-2 text-danger-800"></i> <span class="text-danger-800"> Offline </span> </a></li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="right-icon-tab1">
                            <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('sl') }}</th>
                                        <th>{{ __('driver_name')}}</th>
                                        <th>{{__('vehicle_type')}}</th>
                                        <th>{{__('trip_status')}}</th>
                                        <th>{{__('today_working')}}</th>
                                        <th>{{__('yesterday_working')}}</th>
                                        <th>{{__('weekly_working')}}</th>
                                        <th>{{__('monthly_working')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($drivers_online as $key => $driver)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                     <div>
                                                        <a href="{{ route('driverDetails', $driver->slug)}}" class="text-default font-weight-semibold letter-icon-title">{!! $driver->firstname !!} {!! $driver->lastname !!}
                                                            <br>
                                                            {!! $driver->phone_number !!}
                                                        </a>
                                                        @if($driver->online_by)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> Online</div>
                                                        @else
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> Offline</div>
                                                        @endif

                                                        @if($driver->active == 1)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> Active</div>
                                                        @elseif($driver->active == 0)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> Inactive</div>
                                                        @endif    
                                                    </div>

                                                </div>                                            
                                            </td>
                                            <td>
                                                {{$driver->driver->vehicletype->vehicle_name}}
                                                
                                            </td>
                                            <td>
                                                <div class="text-muted font-size-sm"><span style="color:#2E7D32;">Completed -{{ $driver->trip_completed}}</span></div>
                                                <div class="text-muted font-size-sm"><span style="color:#C62828;">Cancelled -{{ $driver->trip_cancelled}}</span></div>
                                             </td>
                                            <td>{{$driver->today_working}}</td> 
                                            <td>{{$driver->yesterday_working}}</td>
                                            <td>{{$driver->weekhours_working}}</td>
                                            <td>{{$driver->monthhours_working}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="right-icon-tab2">            
                            <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('sl') }}</th>
                                        <th>{{__("driver_name")}}</th>
                                        <th>{{__('Vehicle_type')}}</th>
                                        <th>{{__('Trip_status')}}</th>
                                        <th>{{__('today_working')}}</th>
                                        <th>{{__('yesterday_working')}}</th>
                                        <th>{{__('weekly_working')}}</th>
                                        <th>{{__('monthly_working')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($drivers_offline as $key => $driver)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                     <div>
                                                        <a href="{{ route('driverDetails', $driver->slug)}}" class="text-default font-weight-semibold letter-icon-title">{!! $driver->firstname !!} {!! $driver->lastname !!}
                                                            <br>
                                                            {!! $driver->phone_number !!}
                                                        </a>
                                                        @if($driver->online_by)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> Online</div>
                                                        @else
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> Offline</div>
                                                        @endif

                                                        @if($driver->active == 1)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> Active</div>
                                                        @elseif($driver->active == 0)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> Inactive</div>
                                                        @endif 
                                                    </div>

                                                  
                                                </div>                                            
                                            </td>
                                            <td>
                                                {{$driver->driver->vehicletype->vehicle_name}}
                                                
                                            </td>
                                            <td>
                                                <div class="text-muted font-size-sm"><span style="color:#2E7D32;">Completed -{{ $driver->trip_completed}}</span></div>
                                            
                                                <div class="text-muted font-size-sm"><span style="color:#C62828;">Cancelled -{{ $driver->trip_cancelled}}</span></div>
                                              </td>
                                            <td>{{$driver->today_working}}</td>
                                            <td>{{$driver->yesterday_working}}</td>
                                            <td>{{$driver->weekhours_working}}</td>
                                            <td>{{$driver->monthhours_working}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <span class="card-title font-weight-semibold">Driver Details</span>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <span class="card-title font-weight-semibold">Balance changes</span>
                    <div class="header-elements">
                        <span><i class="icon-arrow-down22 text-danger"></i> <span class="font-weight-semibold">- 29.4%</span></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="canvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div> -->
        <!-- </div> -->
    </div>
</div>
<!-- /horizontal form modal -->

<script type="text/javascript">
    var i =1;
    var message = "{{session()->get('message')}}";

    if(message){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

</script>
<script>
        var config = {
            type: 'line',
            data: {
                labels: ['a','b','c'],
                datasets: [{
                    label: 'BMW',
                    backgroundColor: "rgb(229,26,55,0.5)",
                    borderColor: "rgb(229,26,55,0.5)",
                    data: ['12','31','46'],
                    fill: false,

                }, 
                {
                    label: 'Audi',
                    fill: false,
                    backgroundColor: "rgb(0,0,0,0.5)" ,
                    borderColor: "rgb(0,0,0,0.5)",
                    data: ['32','40','53'],
                },
                {
                    label: 'Auto',
                    fill: false,
                    backgroundColor: "rgb(50,51,255,0.5)" ,
                    borderColor: "rgb(50,51,255,0.5)",
                    data: ['10','20','30'],
                },
                
                ]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    // text: 'Security Attack for past 7 Days'
                },
                legend: {
                    labels: {
                        usePointStyle: true
                    },
                        position: 'bottom',
                },
                                            
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Days'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Drivers based on Type'
                        },
                        suggestedMin: 10,
                        suggestedMax: 200
                    }]
                }
            }
        };

                         
    window.onload = function() {
    var ctx = document.getElementById('canvas').getContext('2d');
    window.myLine = new Chart(ctx, config);
  
    };
</script>

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

        @foreach($drivers_online as $key => $driver)

        if("{{$driver->profile_pic}}" != ""){
       
          getUrlByFileName('{{$driver->profile_pic}}', mimes.jpeg).then(function(data) {
    
        $("#{{$driver->id}}").attr('src',data);
      });
      }
      @endforeach



      @foreach($drivers_offline as $key => $driver)

        if("{{$driver->profile_pic}}" != ""){
       
          getUrlByFileName('{{$driver->profile_pic}}', mimes.jpeg).then(function(data) {
    
        $("#{{$driver->id}}").attr('src',data);
      });
      }
      @endforeach


  </script>


@endsection
