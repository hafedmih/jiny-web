@extends('layouts.app')

@section('content')


<div class="content">
    <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('outstation_list') }}</h5>
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
                    <th>{{ __('date') }}</th>
                    <th>{{ __('trip_from') }}</th>
                    <th>{{ __('trip_to') }}</th>
                    <th>{{__('status')}}</th>
                    <th>{{__('action')}}</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach($outstation as $key => $outstationlist)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>
                            <a style="color:#222" href="{{ route('requestView',$outstationlist->id)}}">
                            {!! $outstationlist->request_number !!} @if($outstationlist->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif
                            </a>
                        </td>
                        
                        <td>
                                <a style="color:#222" href="{{ route('driverDetails', $outstationlist->driverDetail ? $outstationlist->driverDetail->slug : '' )}}">
                            {!! $outstationlist->driverDetail ? $outstationlist->driverDetail->firstname.' '.$outstationlist->driverDetail->lastname : '' !!}
                            </a>
                        </td>
                        <td>
                            <a style="color:#222" href="{{ route('userView',$outstationlist->userDetail ? $outstationlist->userDetail->slug : '')}}">
                                {!! $outstationlist->userDetail ? $outstationlist->userDetail->firstname.' '.$outstationlist->userDetail->lastname : '' !!}
                            </a>
                        </td>
                        <td>{{date("d/m/Y  h:i:s a",strtotime($outstationlist->created_at))}}</td>
                        <td>{!! $outstationlist->requestHistory ? $outstationlist->requestHistory->pick_address: '' !!}</td> 
                        <td>{!! $outstationlist->requestHistory ? $outstationlist->requestHistory->drop_address: '' !!}</td>  
                        <td>
                            @if($outstationlist->is_cancelled == 1)
                                <span class="badge badge-danger">{{ __('trip_cancelled') }}</span>
                            @elseif($outstationlist->is_completed == 1)
                                <span class="badge badge-success">{{ __('trip_completed') }}</span>
                            @elseif($outstationlist->is_trip_start == 1 )
                                <span class="badge badge-warning">{{ __('trip_started') }}</span>
                            @elseif( $outstationlist->is_driver_arrived == 1 )
                                <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                            @elseif( $outstationlist->is_driver_started == 1)
                                <span class="badge badge-warning">{{ __('trip_started') }}</span>
                            @else
                                <span class="badge badge-info">{{ __('trip_created') }}</span>
                            @endif
                        </td>   
                        <td>        
                            <div class="btn-group">   
                               @if(auth()->user()->can('request-view'))
                                 <a href="{{ route('requestView',$outstationlist->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                               @endif   
                            </div>  
                        </td>                     
                    </tr>
                @endforeach
            </tbody>
        </table>    
    </div>
  


@endsection