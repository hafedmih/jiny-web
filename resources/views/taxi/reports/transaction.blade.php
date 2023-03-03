@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.179.0.min.js"></script>


<div class="content">


   
 
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">{{ __('transaction_list') }}</h6>
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
                        @if(auth()->user()->can('reports-earned'))
                        <li class="nav-item "><a href="#right-icon-tab1" class="nav-link active" data-toggle="tab"><i class="icon-stats-growth2 ml-2 text-success-800"></i><span class="text-success-400">{{ __('earned')}}</span></a></li>
                        @endif
                        @if(auth()->user()->can('reports-spend'))
                        <li class="nav-item"><a href="#right-icon-tab2" class="nav-link " data-toggle="tab"><i class="icon-stats-decline2 ml-2 text-danger-800"></i> <span class="text-danger-800">{{ __('spent') }}</span> </a></li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="right-icon-tab1">
                            <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('name') }}</th>
                                        <th>{{ __('purpose') }}</th>
                                        <th>{{ __('amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($earned as $key => $wallet)
                                    <tr>
                                        <td>{{ $wallet->getUser ? $wallet->getUser->firstname :''}} {{ $wallet->getUser ? $wallet->getUser->lastname :'' }}</td>
                                        <td>{{ $wallet->purpose }}</td>
                                        <td>{{ $wallet->amount }}</td>
                                    </tr>
                                    @endforeach
                                
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade show " id="right-icon-tab2">
                            <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('name') }}</th>
                                        <th>{{ __('purpose') }}</th>
                                        <th>{{ __('amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($spent as $key => $wallet)
                                    <tr>
                                        <td>{{ $wallet->getUser ? $wallet->getUser->firstname :''}} {{ $wallet->getUser ? $wallet->getUser->lastname :'' }}</td>
                                        <td>{{ $wallet->purpose }}</td>
                                        <td>{{ $wallet->amount }}</td>
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

@endsection
