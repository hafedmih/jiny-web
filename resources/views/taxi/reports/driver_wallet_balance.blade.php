@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.179.0.min.js"></script>


<div class="content">

   
 
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Driver wallet</h6>
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
                                        <th>{{ __('sl') }}</th>
                                        <th>{{ __('driver_name')}}</th>
                                        <th>{{__('phone_number')}}</th>
                                        <th>{{__('wallet_balance')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($balance  as $key => $value)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{$value->getdriver ? $value->getdriver->firstname :''}} {{$value->getdriver ? $value->getdriver->lastname :''}}</td>
                                            <td>{{$value->getdriver ? $value->getdriver->phone_number:''}}</td>
                                            <td>{{$value->balance_amount}}</td>
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

   


@endsection
