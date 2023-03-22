@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('driver-logs-list') }} </h5>
            <div class="header-elements">
                
            </div>
        </div>
    </div>
    
    <div class="card" id="tableDiv">
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('DriverName') }}</th>
                    <th>{{ __('date') }}</th>
                    <th>{{ __('online') }}</th>
                    <th>{{ __('offline') }}</th>
                    <th>{{ __('working_hours') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($drivers_log as $key => $logs)
                    @if($logs->DriversLog)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{!! $logs->firstname.' '.$logs->lastname !!}</td>
                        <td>{!! $logs->DriversLog ? date("d-m-Y",strtotime($logs->DriversLog->date)) : '' !!}</td>
                        <td>{!! $logs->DriversLog ? date("h:i:s A",strtotime($logs->DriversLog->online_time)) : '' !!}</td>
                        <td>{!! $logs->DriversLog ? date("h:i:s A",strtotime($logs->DriversLog->offline_time)) : '' !!}</td>
                        <td>{!! $logs->DriversLog ? date("H",strtotime($logs->DriversLog->working_time)).' hours '.date("i",strtotime($logs->DriversLog->working_time)).' mins'  : '' !!}</td>
                        <td>                     
                            @if(auth()->user()->can('driver-logs-list'))
                                <a href="{{ route('driverWorkingHours',$logs->slug) }}" class="btn bg-pink-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Logs"> <i class="icon-eye"></i> </a>
                            @endif
                        </td>
                    </tr>
                    @php $i++; @endphp
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

</div>
<!-- /horizontal form modal -->


@endsection
