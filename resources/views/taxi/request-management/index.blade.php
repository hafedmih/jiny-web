@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('Request-management') }}</h5>
            
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-top nav-tabs-bottom nav-justified">
                <li class="nav-item"><a href="#top-justified-tab1" class="nav-link active" data-toggle="tab">{{ __('Ride Now') }}</a></li>
                <li class="nav-item"><a href="#top-justified-tab2" class="nav-link" data-toggle="tab">{{ __('Schedule Trip') }}</a></li>
            </ul>
            
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="top-justified-tab1">
                
                            
                            <table class="table datatable-button-print-columns1" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('Sl')}}
                                        <th>{{ __('Trip Status') }}</th>
                                        <th>{{ __('Request Number') }}</th>
                                        <th>{{ __('User Name') }}</th>
                                        <th>{{ __('Driver Name') }}</th>
                                        <th>{{ __('Payment Option')}}</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($requests as $key => $value)
                                        <tr>
                                             <td>{{ ++$key }}</td>
                                             <td>@if($value->is_completed == 1)
                                                    <span class="badge badge-success">{{ __('completed') }}</span>
                                                @elseif($value->is_cancel == 1)
                                                                <span class="badge badge-danger">{{ __('cancel') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('cancel') }}</span>
                                                @endif
                                            </td>
                                           
                                            <td>{{ $value->request_number }}</td>
                                            <td>{{ $value->userDetail->firstname  }}{{ $value->userDetail->lastname  }}<br>{{ $value->userDetail->phone_number }}</td>
                                            <td>{{ $value->driverDetail->firstname ?? $value['driverDetail->firstname']  }}{{ $value->driverDetail->lastname ??  $value['driverDetail->lastname'] }}<br>{{ $value->driverDetail->phone_number ??  $value['driverDetail->phone_number']  }}</td>
                                            <td>{{ $value->payment_opt }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="top-justified-tab2">
                            <table class="table datatable-button-print-columns1" id="roletable">
                            <thead>
                                    <tr>
                                        <th>{{ __('Sl')}}
                                        <th>{{ __('Trip Status') }}</th>
                                        <th>{{ __('Request Number') }}</th>
                                        <th>{{ __('User Name') }}</th>
                                        <th>{{ __('Driver Name') }}</th>
                                        <th>{{ __('Payment Option')}}</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($request as $key => $value)
                                        <tr>
                                             <td>{{ ++$key }}</td>
                                             <td>@if($value->is_completed == 1)
                                                    <span class="badge badge-success">{{ __('completed') }}</span>
                                                @elseif($value->is_cancel == 1)
                                                                <span class="badge badge-danger">{{ __('cancel') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('cancel') }}</span>
                                                @endif
                                            </td>
                                            
                                            <td>{{ $value->request_number }}</td>
                                            <td>{{ $value->userDetail->firstname  }}{{ $value->userDetail->lastname  }}<br>{{ $value->userDetail->phone_number }}</td>
                                            <td>{{ $value->driverDetail->firstname }}{{ $value->driverDetail->lastname}}<br>{{ $value->driverDetail->phone_number }}</td>
                                            <td>{{ $value->payment_opt }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

        </div>
    </div>

   

</div>


@endsection
