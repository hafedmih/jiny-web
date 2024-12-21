@extends('layouts.app')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">
<style>
    table.dataTable tbody td {
  word-break: break-word; white-space: normal;
}
</style>

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-request') }}</h5>
            
        </div>
    </div>

    <div class="card" id="tableDiv">
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-bottom">
            @php $route = Request::route()->getName(); @endphp
            <li class="nav-item"><a href="#" onclick="routeCall(`{{route('request') }}`)" class="@if (in_array($route, ['request']))  nav-link active @else nav-link  @endif " data-toggle="tab"><i class="icon-racing"></i> {{ __('ride_now')}}</a></li>
				<li class="nav-item"><a href="#" onclick="routeCall(`{{route('requests_later') }}`)" class=" @if (in_array($route, ['requests_later']))  nav-link active @else nav-link  @endif  " data-toggle="tab"><i class="icon-hour-glass2"></i> {{ __('schedule_trip')}}  </a></li>
                <!-- <li class="nav-item"><a href="#justified-right-icon-tab3" onclick="routeCall(`{{route('requests_rental_now') }}`)" class=" @if (in_array($route, ['requests_rental_now']))  nav-link active @else nav-link  @endif  " data-toggle="tab"><i class="icon-forward"></i> {{ __('rental_ride_now_trips')}}</a></li>
                <li class="nav-item"><a href="#justified-right-icon-tab4" onclick="routeCall(`{{route('requests_rental_later') }}`)" class=" @if (in_array($route, ['requests_rental_later']))  nav-link active @else nav-link  @endif  " data-toggle="tab"><i class="icon-hour-glass3"></i> {{ __('rental_ride_later_trips')}}</a></li>
                <li class="nav-item"><a href="#justified-right-icon-tab5" onclick="routeCall(`{{route('outstation_list') }}`)" class=" @if (in_array($route, ['outstation_list']))  nav-link active @else nav-link  @endif  " data-toggle="tab"><i class="icon-direction"></i> {{ __('outstation_trips')}}</a></li> -->
                <li class="nav-item"><a href="#" onclick="routeCall(`{{route('cancelled_trips') }}`)" class=" @if (in_array($route, ['cancelled_trips']))  nav-link active @else nav-link  @endif  " data-toggle="tab"><i class="icon-cancel-circle2"></i> {{ __('cancelled_trips')}}</a></li>
                <li class="nav-item"><a href="#" onclick="routeCall(`{{route('on_going_trips') }}`)" class=" @if (in_array($route, ['on_going_trips']))  nav-link active @else nav-link  @endif  " data-toggle="tab"><i class="icon-meter-fast"></i> {{ __('on_going_trips')}}</a></li>
			</ul>
		    <div class="tab-content">
            @php $route = Request::route()->getName(); @endphp
				<div class="tab-pane fade @if (in_array($route, ['request']))  show active @else show @endif" id="justified-right-icon-tab1">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('request_id') }}</th>
                                <th>{{ __('user_name') }}</th>
                                <th>{{ __('driver_name') }}</th>
                                <th>{{ __('date') }}</th>
                                <th>{{ __('pickup_address') }}</th>
                                <th>{{ __('drop_address') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result['requests_now'] as $key => $request)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @else
                                            <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(auth()->user()->hasRole("Super Admin"))
                                            <a style="color:#222" href="{{ route('userView',$request->userDetail ? $request->userDetail->slug :'')}}">
                                                {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                        @else
                                            <a style="color:#222" href="#">
                                                {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <a style="color:#222" href="{{ route('driverDetails', $request->driverDetail ? $request->driverDetail->slug : '' )}}">
                                        {!! $request->driverDetail ? $request->driverDetail->firstname.' '.$request->driverDetail->lastname : '' !!}
                                        </a>
                                    </td>
                                    <td>{{date("d/m/Y  h:i:s a",strtotime($request->created_at))}}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->pick_address : '' !!}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->drop_address : '' !!}</td>
                                    <td>
                                        @if($request->is_cancelled == 1)
                                            <span class="badge badge-danger">{{ __('trip_cancelled') }}</span><br>
                                            <span class="text-danger">{{ $request->cancel_method }}</span>
                                        @elseif($request->is_completed == 1)
                                            <span class="badge badge-success">{{ __('trip_completed') }}</span>
                                        @elseif($request->is_trip_start == 1 )
                                            <span class="badge badge-warning">{{ __('trip_started') }}</span>
                                        @elseif( $request->is_driver_arrived == 1 )
                                            <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                                        @elseif( $request->is_driver_started == 1)
                                            <span class="badge badge-primary">{{ __('trip_accepted') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('trip_created') }}</span>
                                        @endif
                                    </td>  
                                    <td>        
                                        <div class="btn-group">   
                                        @if(auth()->user()->can('request-view'))
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a href="{{ route('requestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                        @endif    
                                        @endif    
                                        @if(auth()->user()->can('dispatcher-edit'))
                                            @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                            @endif   
                                        @endif    
                                        </div>
                                    </td>
            
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(!empty($result['requests_now']))
                        {{ $result['requests_now']->links('vendor.pagination.bootstrap-4')}}
                    @endif
				</div>
				<div class="tab-pane fade @if (in_array($route, ['requests_later']))  show active @else show @endif" id="justified-right-icon-tab2">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('request_id') }}</th>
                                <th>{{ __('user_name') }}</th>
                                <th>{{ __('driver_name') }}</th>
                                <th>{{ __('date') }}</th>
                                <th>{{ __('pickup_address') }}</th>
                                <th>{{ __('drop_address') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result['requests_later'] as $key => $request)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @else
                                            <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->userDetail)
                                        @if(auth()->user()->hasRole("Super Admin"))
                                            <a style="color:#222" href="{{ route('userView',$request->userDetail->slug)}}">
                                                {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                        @else
                                        <a style="color:#222" href="#">
                                            {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                        </a>
                                        @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a style="color:#222" href="{{ route('driverDetails', $request->driverDetail ? $request->driverDetail->slug : '' )}}">
                                        {!! $request->driverDetail ? $request->driverDetail->firstname.' '.$request->driverDetail->lastname : '' !!}
                                        </a>
                                    </td>
                                    <td>{{date("d/m/Y  h:i:s a",strtotime($request->created_at))}}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->pick_address : '' !!}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->drop_address : '' !!}</td>
                                    <td>
                                        @if($request->is_cancelled == 1)
                                            <span class="badge badge-danger">{{ __('trip_cancelled') }}</span><br>
                                            <span class="text-danger">{{ $request->cancel_method }}</span>
                                        @elseif($request->is_completed == 1)
                                            <span class="badge badge-success">{{ __('trip_completed') }}</span>
                                        @elseif($request->is_trip_start == 1 || $request->is_driver_arrived == 1 || $request->is_driver_started == 1)
                                            <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('trip_created') }}</span>
                                        @endif
                                    </td>  
                                    <td>     
                                        <div class="btn-group">   
                                        @if(auth()->user()->can('request-view'))
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a href="{{ route('requestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                        @endif  
                                        @endif  
                                        @if(auth()->user()->can('dispatcher-edit'))
                                            @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                            @endif   
                                        @endif   
                                        @if(auth()->user()->can('request-delete'))
                                        @if(!$request->is_driver_started && !$request->is_cancelled)
                                            <a href="#" onclick="Javascript: return deleteAction('$request->id', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple ml-1" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete Trip"> <i class="icon-trash"></i> </a>
                                        @endif 
                                        @endif
                                        </div>
                                    </td>
            
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(!empty($result['requests_later']))
                        {{ $result['requests_later']->links('vendor.pagination.bootstrap-4')}}
                    @endif
				</div>
                <div class="tab-pane fade @if (in_array($route, ['requests_rental_now']))  show active @else show @endif" id="justified-right-icon-tab3">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('request_id') }}</th>
                                <th>{{ __('user_name') }}</th>
                                <th>{{ __('driver_name') }}</th>
                                <th>{{ __('date') }}</th>
                                <th>{{ __('pickup_address') }}</th>
                                <th>{{ __('drop_address') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result['requests_rental_now'] as $key => $request)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @else
                                            <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->userDetail)
                                            @if(auth()->user()->hasRole("Super Admin"))
                                            <a style="color:#222" href="{{ route('userView',$request->userDetail->slug)}}">
                                                {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                            @else
                                            <a style="color:#222" href="#">
                                                {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a style="color:#222" href="{{ route('driverDetails', $request->driverDetail ? $request->driverDetail->slug : '' )}}">
                                        {!! $request->driverDetail ? $request->driverDetail->firstname.' '.$request->driverDetail->lastname : '' !!}
                                        </a>
                                    </td>
                                    <td>{{date("d/m/Y  h:i:s a",strtotime($request->created_at))}}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->pick_address : '' !!}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->drop_address : '' !!}</td>
                                    <td>
                                        @if($request->is_cancelled == 1)
                                            <span class="badge badge-danger">{{ __('trip_cancelled') }}</span><br>
                                            <span class="text-danger">{{ $request->cancel_method }}</span>
                                        @elseif($request->is_completed == 1)
                                            <span class="badge badge-success">{{ __('trip_completed') }}</span>
                                        @elseif($request->is_trip_start == 1)
                                            <label class="badge badge-warning ">{{ __('trip_started')}}</label>
                                        @elseif($request->is_driver_arrived == 1 )
                                            <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                                        @elseif($request->is_driver_started == 1)
                                            <label class="badge badge-info">{{ __('trip_accepted')}}</label>
                                        @else
                                            <span class="badge badge-info">{{ __('trip_created') }}</span>
                                        @endif
                                    </td>  
                                    
                                
                                    <td>     
                                        <div class="btn-group">   
                                        @if(auth()->user()->can('request-view'))
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a href="{{ route('requestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                        @endif  
                                        @endif  
                                        @if(auth()->user()->can('dispatcher-edit'))
                                            @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                            @endif   
                                        @endif   
                                        @if(auth()->user()->can('request-delete'))
                                        @if(!$request->is_driver_started && !$request->is_cancelled)
                                            <a href="#" onclick="Javascript: return deleteAction('$request->id', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple ml-1" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete Trip"> <i class="icon-trash"></i> </a>
                                        @endif 
                                        @endif    
                                        <!-- @if(auth()->user()->can('request-edit'))
                                        @if(!$request->is_cancelled && !$request->is_completed)
                                            <a href="#" onclick="Javascript: return editAction(`{{ route('requestEnd',$request->id) }}`)" class="btn bg-primary-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-checkmark4"></i> </a>
                                        @endif  
                                        @endif   -->
                                        </div>
                                    </td>
            
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(!empty($result['requests_rental_now']))
                        {{ $result['requests_rental_now']->links('vendor.pagination.bootstrap-4')}}
                    @endif
				</div>
                <div class="tab-pane fade @if (in_array($route, ['requests_rental_later']))  show active @else show @endif" id="justified-right-icon-tab4">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('request_id') }}</th>
                                <th>{{ __('user_name') }}</th>
                                <th>{{ __('driver_name') }}</th>
                                <th>{{ __('date') }}</th>
                                <th>{{ __('pickup_address') }}</th>
                                <th>{{ __('drop_address') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result['requests_rental_later'] as $key => $request)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @else
                                            <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->userDetail)
                                            @if(auth()->user()->hasRole("Super Admin"))
                                            <a style="color:#222" href="{{ route('userView',$request->userDetail->slug)}}">
                                                {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                            @else
                                            <a style="color:#222" href="#">
                                            {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a style="color:#222" href="{{ route('driverDetails', $request->driverDetail ? $request->driverDetail->slug : '' )}}">
                                        {!! $request->driverDetail ? $request->driverDetail->firstname.' '.$request->driverDetail->lastname : '' !!}
                                        </a>
                                    </td>
                                    <td>{{date("d/m/Y  h:i:s a",strtotime($request->created_at))}}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->pick_address : '' !!}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->drop_address : '' !!}</td>
                                    <td>
                                        @if($request->is_cancelled == 1)
                                            <span class="badge badge-danger">{{ __('trip_cancelled') }}</span><br>
                                            <span class="text-danger">{{ $request->cancel_method }}</span>
                                        @elseif($request->is_completed == 1)
                                            <span class="badge badge-success">{{ __('trip_completed') }}</span>
                                        @elseif($request->is_trip_start == 1 || $request->is_driver_arrived == 1 || $request->is_driver_started == 1)
                                            <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('trip_created') }}</span>
                                        @endif
                                    </td>  
                                    <td>     
                                        <div class="btn-group">   
                                        @if(auth()->user()->can('request-view'))
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a href="{{ route('requestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                        @endif  
                                        @endif   
                                        @if(auth()->user()->can('dispatcher-edit'))
                                            @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                            @endif   
                                        @endif  
                                        @if(auth()->user()->can('request-delete'))
                                        @if(!$request->is_driver_started && !$request->is_cancelled)
                                            <a href="#" onclick="Javascript: return deleteAction('$request->id', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple ml-1" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete Trip"> <i class="icon-trash"></i> </a>
                                        @endif 
                                        @endif    
                                        <!-- @if(auth()->user()->can('request-edit'))
                                        @if(!$request->is_cancelled && !$request->is_completed)
                                            <a href="#" onclick="Javascript: return editAction(`{{ route('requestEnd',$request->id) }}`)" class="btn bg-primary-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-checkmark4"></i> </a>
                                        @endif  
                                        @endif   -->
                                        </div>
                                    </td>
            
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(!empty($result['requests_rental_later']))
                        {{ $result['requests_rental_later']->links('vendor.pagination.bootstrap-4')}}
                    @endif
				</div>
                <div class="tab-pane fade @if (in_array($route, ['outstation_list']))  show active @else show @endif" id="justified-right-icon-tab5">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('request_id') }}</th>
                                <th>{{ __('user_name') }}</th>
                                <th>{{ __('driver_name') }}</th>
                                <th>{{ __('date') }}</th>
                                <th>{{ __('pickup_address') }}</th>
                                <th>{{ __('drop_address') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result['outstation_list'] as $key => $request)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @else
                                            <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @endif
                                    <td>
                                        @if($request->userDetail)
                                        
                                        @if(auth()->user()->hasRole("Super Admin"))
                                            <a style="color:#222" href="{{ route('userView',$request->userDetail->slug)}}">
                                                {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                            @else
                                            <a style="color:#222" href="#">
                                            {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a style="color:#222" href="{{ route('driverDetails', $request->driverDetail ? $request->driverDetail->slug : '' )}}">
                                        {!! $request->driverDetail ? $request->driverDetail->firstname.' '.$request->driverDetail->lastname : '' !!}
                                        </a>
                                    </td>
                                    <td>{{date("d/m/Y  h:i:s a",strtotime($request->created_at))}}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->pick_address : '' !!}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->drop_address : '' !!}</td>
                                    <td>
                                        @if($request->is_cancelled == 1)
                                            <span class="badge badge-danger">{{ __('trip_cancelled') }}</span><br>
                                            <span class="text-danger">{{ $request->cancel_method }}</span>
                                        @elseif($request->is_completed == 1)
                                            <span class="badge badge-success">{{ __('trip_completed') }}</span>
                                        @elseif($request->is_trip_start == 1 || $request->is_driver_arrived == 1 || $request->is_driver_started == 1)
                                            <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('trip_created') }}</span>
                                        @endif
                                    </td>  
                                    <td>     
                                        <div class="btn-group">   
                                        @if(auth()->user()->can('request-view'))
                                            <a href="{{ route('requestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                        @endif   
                                        @if(auth()->user()->can('dispatcher-edit'))
                                            @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                            @endif   
                                        @endif  
                                        @if(auth()->user()->can('request-delete'))
                                        @if(!$request->is_driver_started && !$request->is_cancelled)
                                            <a href="#" onclick="Javascript: return deleteAction('$request->id', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple ml-1" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete Trip"> <i class="icon-trash"></i> </a>
                                        @endif 
                                        @endif    
                                        <!-- @if(auth()->user()->can('request-edit'))
                                        @if(!$request->is_cancelled && !$request->is_completed)
                                            <a href="#" onclick="Javascript: return editAction(`{{ route('requestEnd',$request->id) }}`)" class="btn bg-primary-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-checkmark4"></i> </a>
                                        @endif  
                                        @endif   -->
                                        </div>
                                    </td>
            
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(!empty($result['outstation_list']))
                        {{ $result['outstation_list']->links('vendor.pagination.bootstrap-4')}}
                    @endif
				</div>
                <div class="tab-pane fade @if (in_array($route, ['cancelled_trips']))  show active @else show @endif" id="justified-right-icon-tab6">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('request_id') }}</th>
                                <th>{{ __('user_name') }}</th>
                                <th>{{ __('driver_name') }}</th>
                                <th>{{ __('date') }}</th>
                                <th>{{ __('pickup_address') }}</th>
                                <th>{{ __('drop_address') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result['cancelled_trips'] as $key => $request)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @else
                                            <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->userDetail)
                                        
                                        @if(auth()->user()->hasRole("Super Admin"))
                                            <a style="color:#222" href="{{ route('userView',$request->userDetail->slug)}}">
                                            {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                              </a>
                                            @else
                                            <a style="color:#222" href="#">
                                            {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a style="color:#222" href="{{ route('driverDetails', $request->driverDetail ? $request->driverDetail->slug : '' )}}">
                                        {!! $request->driverDetail ? $request->driverDetail->firstname.' '.$request->driverDetail->lastname : '' !!}
                                        </a>
                                    </td>
                                    <td>{{date("d/m/Y  h:i:s a",strtotime($request->created_at))}}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->pick_address : '' !!}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->drop_address : '' !!}</td>
                                    <td>
                                        @if($request->is_cancelled == 1)
                                            <span class="badge badge-danger">{{ __('trip_cancelled') }}</span><br>
                                            <span class="text-danger">{{ $request->cancel_method }}</span>
                                        @elseif($request->is_completed == 1)
                                            <span class="badge badge-success">{{ __('trip_completed') }}</span>
                                        @elseif($request->is_trip_start == 1 || $request->is_driver_arrived == 1 || $request->is_driver_started == 1)
                                            <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('trip_created') }}</span>
                                        @endif
                                    </td>  
                                    <td>     
                                        <div class="btn-group">   
                                        @if(auth()->user()->can('request-view'))
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a href="{{ route('requestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                        @endif  
                                        @endif   
                                        @if(auth()->user()->can('dispatcher-edit'))
                                            @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                            @endif   
                                        @endif  
                                        @if(auth()->user()->can('request-delete'))
                                        @if(!$request->is_driver_started && !$request->is_cancelled)
                                            <a href="#" onclick="Javascript: return deleteAction('$request->id', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple ml-1" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete Trip"> <i class="icon-trash"></i> </a>
                                        @endif 
                                        @endif    
                                        <!-- @if(auth()->user()->can('request-edit'))
                                        @if(!$request->is_cancelled && !$request->is_completed)
                                            <a href="#" onclick="Javascript: return editAction(`{{ route('requestEnd',$request->id) }}`)" class="btn bg-primary-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-checkmark4"></i> </a>
                                        @endif  
                                        @endif   -->
                                        </div>
                                    </td>
            
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(!empty($result['cancelled_trips']))
                        {{ $result['cancelled_trips']->links('vendor.pagination.bootstrap-4')}}
                    @endif
				</div>
                <div class="tab-pane fade @if (in_array($route, ['on_going_trips']))  show active @else show @endif" id="justified-right-icon-tab7">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>{{ __('sl') }}</th>
                                <th>{{ __('request_id') }}</th>
                                <th>{{ __('user_name') }}</th>
                                <th>{{ __('driver_name') }}</th>
                                <th>{{ __('date') }}</th>
                                <th>{{ __('pickup_address') }}</th>
                                <th>{{ __('drop_address') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result['on_going_trips'] as $key => $request)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @else
                                            <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->if_dispatch)<div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> By Dispatcher</div> @endif </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->userDetail)
                                        @if(auth()->user()->hasRole("Super Admin"))
                                            <a style="color:#222" href="{{ route('userView',$request->userDetail->slug)}}">
                                            {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                              </a>
                                            @else
                                            <a style="color:#222" href="#">
                                            {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                            </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a style="color:#222" href="{{ route('driverDetails', $request->driverDetail ? $request->driverDetail->slug : '' )}}">
                                        {!! $request->driverDetail ? $request->driverDetail->firstname.' '.$request->driverDetail->lastname : '' !!}
                                        </a>
                                    </td>
                                    <td>{{date("d/m/Y  h:i:s a",strtotime($request->created_at))}}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->pick_address : '' !!}</td>
                                    <td>{!! $request->requestPlace ? $request->requestPlace->drop_address : '' !!}</td>
                                    <td>
                                        @if($request->is_cancelled == 1)
                                            <span class="badge badge-danger">{{ __('trip_cancelled') }}</span>
                                        @elseif($request->is_completed == 1)
                                            <span class="badge badge-success">{{ __('trip_completed') }}</span>
                                        @elseif($request->is_trip_start == 1 || $request->is_driver_arrived == 1 || $request->is_driver_started == 1)
                                            <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('trip_created') }}</span>
                                        @endif
                                    </td>  
                                    <td>     
                                        <div class="btn-group">   
                                        @if(auth()->user()->can('request-view'))
                                        @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                            <a href="{{ route('requestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                        @endif  
                                        @endif   
                                        @if(auth()->user()->can('dispatcher-edit'))
                                            @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                            @endif   
                                        @endif  
                                        @if(auth()->user()->can('request-delete'))
                                        @if(!$request->is_driver_started && !$request->is_cancelled)
                                            <a href="#" onclick="Javascript: return deleteAction('$request->id', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple ml-1" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete Trip"> <i class="icon-trash"></i> </a>
                                        @endif 
                                        @endif    
                                        <!-- @if(auth()->user()->can('request-edit'))
                                        @if(!$request->is_cancelled && !$request->is_completed)
                                            <a href="#" onclick="Javascript: return editAction(`{{ route('requestEnd',$request->id) }}`)" class="btn bg-primary-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-checkmark4"></i> </a>
                                        @endif  
                                        @endif   -->
                                        </div>
                                    </td>
            
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(!empty($result['on_going_trips']))
                        {{ $result['on_going_trips']->links('vendor.pagination.bootstrap-4')}}
                    @endif
				</div>
			</div>
        </div>
    </div>

</div>
<!-- Horizontal form modal -->
<div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">Change Request Category</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <form id="roleForm" name="roleForm" action="{{ route('requestCategoryChange') }}" method="post" class="form-horizontal">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="col-form-label">{{ __('request_id') }}</label>
                                <input type="text" name="request_number" id="title" class="form-control" readonly placeholder="{{ __('request_id') }}" >
                                <input type="hidden" name="request_id" id="request_id">
                                <input type="hidden" name="package_id" id="package_id">
                            </div>
                            <div class="col-md-6 form-group"><br><br>
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="manual_trip" class="form-input-styled required checkeds" data-fouc value="YES">
                                        Rental
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row packeges"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn1" onclick="Javascript: return categoryChange()" class="btn bg-primary">{{ __('save-changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- /horizontal form modal -->

<script type="text/javascript">
    function routeCall(url){
    document.location.href=url;
}

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

    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                console.log(data);
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    $(document).on('click','.categoryChange',function(){
        var id = $(this).attr('id');
        var values = $(this).attr('data-value');
        $("#request_id").val(id);
        $("#title").val(values);
        $('#roleModel').modal('show');
    })

    $(document).on('click','.checkeds',function(){
        var id = $("#request_id").val();
        if($('input[name=manual_trip]').is(':checked')){
            $.ajax({
                url: "{{ url('request-views')}}/"+id,
                type: "GET",
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    var text = '';
                    $.each(data.packages, function( index, value ) {
                        text += "<div class='col-md-3'><div class='card card-body cardpackage' id='"+value.id+"'><h3>"+value.get_package.name+" ("+value.get_package.get_country.currency_symbol+" "+value.price+")</h3><span>"+value.get_package.hours+" Hr / "+value.get_package.km+" Km</span> </div></div>";   
                    });
                    $(".packeges").html(text);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
        else{
            $(".packeges").html('');
        }
    });

    $(document).on('click',".cardpackage",function(){
        $(".cardpackage").removeClass('bg-success');
        $(this).addClass('bg-success');
        $("#package_id").val($(this).attr('id'));
    })

    function categoryChange(){
        $.ajax({
            url: "{{ route('requestCategoryChange')}}",
            type: "POST",
            data: $("#roleForm").serialize(),
            dataType: 'json',
            success: function (data) {
                console.log(data);
                swal({
                    title: "Success",
                    text: data.message,
                    icon: "success",
                }).then((value) => {        
                    // $('#roleModel').modal('hide');
                    // $("#request_id").val('');
                    // $("#title").val('');
                    // $(".packeges").html('');
                    // $('input[name=manual_trip]').prop('checked', false);
                    location.reload();
                });
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

</script>

@endsection
