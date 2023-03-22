@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.179.0.min.js"></script>


<div class="content">

   
 
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">{{ __('trip_reports') }}</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                            <thead>
                                <tr>
                                    <th rowspan="2">{{ __('date') }}</th> <!-- merge two rows -->
                                    <th colspan="2" scope="colgroup">{{ __('dispatcher_booking') }}</th> <!-- merge four columns -->
                                    <th colspan="2" scope="colgroup">{{ __('mobile_booking') }}</th>
                                </tr>
                                <tr>
                                    <th scope="col-6">{{ __('completed') }}</th>
                                    <th scope="col-6">{{ __('cancelled') }}</th>
                                    <th scope="col-4">{{ __('completed') }}</th>
                                    <th scope="col-4">{{ __('cancelled') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trips as $value)
                                    <tr>
                                        <td>{{$value->date}}</td>
                                        <td>{{$value->completed}}</td>
                                        <td>{{$value->cancelled}}</td>
                                        <td>{{$value->mobile_completed}}</td>
                                        <td>{{$value->mobile_cancelled}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
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

       



    


  </script>


@endsection
