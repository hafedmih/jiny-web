@extends('layouts.app')

@section('content')

<div class="content">
    <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('cancel-request-list') }}</h5>
                <div class="header-elements">
                    <div class="list-icons">
                    </div>
                </div>
            </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('s.no') }}</th>
                    <th>{{ __('request_details') }}</th>
                    <th>{{ __('user_details') }}</th>
                    <th>{{ __('driver_details') }}</th>
                    <!-- <th>{{ __('cancel_by') }}</th> -->
                    <th>{{ __('cancel_reson') }}</th>
                    <th>{{ __('distance') }}</th>
                    <th>{{ __('user-location')}}</th>
                    <th>{{ __('driver-location')}}</th>
                    <!-- <th>{{ __('Date')}}</th> -->
                    <!-- <th>{{ __('status') }}</th> -->
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $key => $list)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>
                            <div><a style="color:#000" href="{{ route('requestView',$list->request_id) }}">{!! $list->requestDetails ? $list->requestDetails->request_number : '' !!}</a><br> 
                            <span><p>Cancelled By:{!! $list->cancelled_by !!}</p></span>
                            {!! date('d-M-y', strtotime($list->created_at)) !!}
                            </div>
                            
                        <td>
                            <div><a style="color:#000" href="{{ route('userView',$list->requestDetails ?  ($list->requestDetails->userDetail ? $list->requestDetails->userDetail->slug : '') : '')}}">
                            {!! $list->requestDetails ? ($list->requestDetails->userDetail ? $list->requestDetails->userDetail->firstname.' '.$list->requestDetails->userDetail->lastname : '') : '' !!}</a>
                            <br>
                            {!! $list->requestDetails ? ($list->requestDetails->userDetail ? $list->requestDetails->userDetail->phone_number  : '') : '' !!}
                            </div>
                        </td>
                        <td>
                            <div><a style="color:#000" href="{{ route('driverDetails',$list->requestDetails ? ($list->requestDetails->driverDetail ? $list->requestDetails->driverDetail->slug : '') : '')}}">
                            {!! $list->requestDetails ? ($list->requestDetails->driverDetail ? $list->requestDetails->driverDetail->firstname.' '.$list->requestDetails->driverDetail->lastname : '') : '' !!}</a>
                            <br>
                            {!! $list->requestDetails ? ($list->requestDetails->driverDetail ? $list->requestDetails->driverDetail->phone_number  : '') : '' !!}
                            </div>
                        </td>
                        <!-- <td>{!! $list->cancelled_by !!}</td> -->
                        <td>{!! $list->resonDetails ? $list->resonDetails->reason  : '' !!}</td>
                        <td>{!! $list->distance !!}</td>
                        <td>{!! $list->user_location ? $list->user_location : '' !!}</td>
                        <td>{!! $list->driver_location ? $list->driver_location : '' !!}</td>
                        <!-- <td>{!! $list->created_at!!}</td> -->
                        <!-- <td>
                            @if($list['status'] != "Pending")
                                <span class="badge badge-success">{{ $list['status'] }}</span>
                            @else
                                <span class="badge badge-danger">{{ $list['status'] }}</span>
                            @endif
                        </td> -->
                        <td>                               
                        <div class="btn-group">   
                                        @if(auth()->user()->can('request-view'))
                                            <a href="{{ route('requestView',$list->request_id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                        @endif  
                        </div>                  
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>    
    </div>
    
</div>



@endsection