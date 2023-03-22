@extends('layouts.app')

@section('content')


<div class="content">
    <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('rental_list') }}</h5>
                <div class="header-elements">
                    <!-- <div class="list-icons">
                        @if(auth()->user()->can('new-subscription'))
                           <a href="#"> <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i>{{ __('add-new')}}</button></a>
                        @endif
                    </div> -->
                </div>
            </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{__('request_id')}}</th>
                    <th>{{ __('driver_name') }}</th>
                    <th>{{ __('user_name') }}</th>
                    <th>{{ __('date')}}</th>
                    <th>{{ __('trip_from') }}</th>
                    <th>{{ __('trip_to') }}</th>
                    <th>{{__('status')}}</th>
                    <th>{{__('action')}}</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach($rental as $key => $rentallist)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>
                            <a style="color:#222" href="{{ route('requestView',$rentallist->id)}}">
                            {!! $rentallist->request_number!!} </a>
                        </td>
                        <td>
                                <a style="color:#222" href="{{ route('driverDetails', $rentallist->driverDetail ? $rentallist->driverDetail->slug : '' )}}">
                            {!! $rentallist->driverDetail ? $rentallist->driverDetail->firstname : '' !!}
                            </a>
                        </td>
                         <td>
                            <a style="color:#222" href="{{ route('userView',$rentallist->userDetail->slug)}}">
                                {!! $rentallist->userDetail ? $rentallist->userDetail->firstname : '' !!}
                            </a>
                        </td>
                        
                        <td>{{date("d/m/Y  h:i:s a",strtotime($rentallist->created_at))}}</td>
                        <td>{!! $rentallist->requestHistory ? $rentallist->requestHistory->pick_address : '' !!}</td> 
                        <td>{!! $rentallist->requestHistory ? $rentallist->requestHistory->drop_address: '' !!}</td>  
                        <td>
                            @if($rentallist->is_cancelled == 1)
                                <span class="badge badge-danger">{{ __('trip_cancelled') }}</span>
                            @elseif($rentallist->is_completed == 1)
                                <span class="badge badge-success">{{ __('trip_completed') }}</span>
                            @elseif($rentallist->is_trip_start == 1 )
                                <span class="badge badge-warning">{{ __('trip_started') }}</span>
                            @elseif( $rentallist->is_driver_arrived == 1 )
                                <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                            @elseif( $rentallist->is_driver_started == 1)
                                <span class="badge badge-warning">{{ __('trip_started') }}</span>
                            @else
                                <span class="badge badge-info">{{ __('trip_created') }}</span>
                            @endif
                        </td>   
                        <td>        
                            <div class="btn-group">   
                               @if(auth()->user()->can('request-view'))
                                 <a href="{{ route('requestView',$rentallist->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                               @endif   
                            </div>  
                        </td>                     
                    </tr>
                @endforeach
            </tbody>
        </table>    
    </div>
  


@endsection