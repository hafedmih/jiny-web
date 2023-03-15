@extends('layouts.dispatcher-layout')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">


<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('dispatcher-history') }}</h5>
            
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tableDiv">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-bottom">
                        <li class="nav-item"><a href="#justified-right-icon-tab1" class="nav-link active" data-toggle="tab"><i class="icon-menu7"></i> Ride Now Trips </a></li>
                        <li class="nav-item"><a href="#justified-right-icon-tab2" class="nav-link" data-toggle="tab"><i class="icon-mention"></i> Schedule Trips </a></li>
                        <!-- <li class="nav-item"><a href="#justified-right-icon-tab3" class="nav-link" data-toggle="tab"><i class="icon-mention"></i> Rental Ride now Trips </a></li>
                        <li class="nav-item"><a href="#justified-right-icon-tab4" class="nav-link" data-toggle="tab"><i class="icon-mention"></i> Rental Ride Later Trips </a></li>
                        <li class="nav-item"><a href="#justified-right-icon-tab5" class="nav-link" data-toggle="tab"><i class="icon-mention"></i> Outstation Trips </a></li> -->
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="justified-right-icon-tab1">
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
                                    @foreach($requests_now as $key => $request) 
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
                                                @else
                                                    <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
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
                                                @if($request->driverDetail)
                                                 <a style="color:#222" href="{{ route('driverDetails',$request->driverDetail->slug)}}">
                                                {!! $request->driverDetail ? $request->driverDetail->firstname.' '.$request->driverDetail->lastname : '' !!}
                                                </a>
                                                @endif
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
                                                    <span class="badge badge-warning">{{ __('trip_started') }}</span>
                                                @elseif($request->is_driver_arrived == 1)
                                                    <span class="badge badge-warning">{{ __('driver_arrived') }}</span>
                                                @elseif($request->is_driver_started == 1)
                                                    <span class="badge badge-primary">{{ __('driver_accepted') }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ __('trip_created') }}</span>
                                                @endif
                                            </td>  
                                            <td>        
                                                <div class="btn-group">  
                                                @if(auth()->user()->can('dispatcherrequest-view'))
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a href="{{ route('dispatchRequestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                                @endif      
                                                @endif  
                                                @if(auth()->user()->can('dispatcher-edit'))
                                                @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                    <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                                @endif   
                                                @endif       
                                                @if(auth()->user()->can('dispatcher-delete'))
                                                    @if($request->is_completed == 0 && $request->is_cancelled == 0)
                                                    <a href="#" onclick="Javascript: return deleteAction('$request->id', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Cancel Trip"> <i class="icon-x"></i> </a>
                                                    @endif
                                                @endif 
                                                </div>
                                            </td>
                    
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="justified-right-icon-tab2">
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
                                    @foreach($requests_later as $key => $request)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
                                                @else
                                                    <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
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
                                                    <span class="badge badge-warning">{{ __('trip_started') }}</span>
                                                @elseif($request->is_driver_arrived == 1)
                                                    <span class="badge badge-warning">{{ __('driver_arrived') }}</span>
                                                @elseif($request->is_driver_started == 1)
                                                    <span class="badge badge-primary">{{ __('driver_accepted') }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ __('trip_created') }}</span>
                                                @endif
                                            </td>  
                                            <td>      
                                                <div class="btn-group">  
                                                @if(auth()->user()->can('dispatcherrequest-view'))
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a href="{{ route('dispatchRequestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                                @endif      
                                                @endif      
                                                @if(auth()->user()->can('dispatcher-edit'))
                                                @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                    <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                                @endif   
                                                @endif   
                                                @if(auth()->user()->can('dispatcher-delete'))
                                                    @if($request->is_completed == 0 && $request->is_cancelled == 0)
                                                    <a href="#" onclick="Javascript: return deleteAction('$documents->slug', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Cancel Trip"> <i class="icon-x"></i> </a>
                                                    @endif
                                                @endif 
                                                </div>
                                            </td>
                    
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="justified-right-icon-tab3">
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
                                    @foreach($requests_rental_now as $key => $request)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
                                                @else
                                                    <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if(auth()->user()->hasRole("Super Admin"))
                                                    <a style="color:#222" href="{{ route('userView',$request->userDetail ? $request->userDetail->slug : '')}}">
                                                    {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                                    </a>
                                                    @else
                                                    <a style="color:#222" href="#">
                                                    {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                 <a style="color:#222" href="{{ route('driverDetails',$request->driverDetail ? $request->driverDetail->slug : '')}}">
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
                                                    <span class="badge badge-warning">{{ __('trip_started') }}</span>
                                                @elseif($request->is_driver_arrived == 1)
                                                    <span class="badge badge-warning">{{ __('driver_arrived') }}</span>
                                                @elseif($request->is_driver_started == 1)
                                                    <span class="badge badge-primary">{{ __('driver_accepted') }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ __('trip_created') }}</span>
                                                @endif
                                            </td>  
                                            <td>      
                                                <div class="btn-group">  
                                                @if(auth()->user()->can('dispatcherrequest-view'))
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a href="{{ route('dispatchRequestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                                @endif      
                                                @endif      
                                                @if(auth()->user()->can('dispatcher-edit'))
                                                @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                    <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                                @endif   
                                                @endif   
                                                @if(auth()->user()->can('dispatcher-delete'))
                                                    @if($request->is_completed == 0 && $request->is_cancelled == 0)
                                                    <a href="#" onclick="Javascript: return deleteAction('$documents->slug', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Cancel Trip"> <i class="icon-x"></i> </a>
                                                    @endif
                                                @endif 
                                                </div>
                                            </td>
                    
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="justified-right-icon-tab4">
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
                                    @foreach($requests_rental_later as $key => $request)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
                                                @else
                                                    <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
                                                @endif
                                            </td>
                                            <td>
                                                
                                                @if(auth()->user()->hasRole("Super Admin"))
                                                    <a style="color:#222" href="{{ route('userView',$request->userDetail ? $request->userDetail->slug : '')}}">
                                                    {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                                    </a>
                                                @else
                                                    <a style="color:#222" href="#">
                                                    {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                 <a style="color:#222" href="{{ route('driverDetails',$request->driverDetail ? $request->driverDetail->slug : '' )}}">
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
                                                    <span class="badge badge-warning">{{ __('trip_started') }}</span>
                                                @elseif($request->is_driver_arrived == 1)
                                                    <span class="badge badge-warning">{{ __('driver_arrived') }}</span>
                                                @elseif($request->is_driver_started == 1)
                                                    <span class="badge badge-primary">{{ __('driver_accepted') }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ __('trip_created') }}</span>
                                                @endif
                                            </td>  
                                            <td>      
                                                <div class="btn-group">  
                                                @if(auth()->user()->can('dispatcherrequest-view'))
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a href="{{ route('dispatchRequestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                                @endif      
                                                @endif      
                                                @if(auth()->user()->can('dispatcher-edit'))
                                                @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                    <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                                @endif   
                                                @endif   
                                                @if(auth()->user()->can('dispatcher-delete'))
                                                    @if($request->is_completed == 0 && $request->is_cancelled == 0)
                                                    <a href="#" onclick="Javascript: return deleteAction('$documents->slug', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Cancel Trip"> <i class="icon-x"></i> </a>
                                                    @endif
                                                @endif 
                                                </div>
                                            </td>
                    
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="justified-right-icon-tab5">
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
                                    @foreach($oustation_list as $key => $request)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a style="color:#222" href="{{ route('requestView',$request->id)}}">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
                                                @else
                                                    <a style="color:#222" href="#">{!! $request->request_number!!} @if($request->availables_status && $request->is_cancelled == 0)<div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> {{ __('driver-available') }}</div> @endif </a>
                                                @endif
                                            </td>
                                           <td>
                                                 
                                                @if(auth()->user()->hasRole("Super Admin"))
                                                    <a style="color:#222" href="{{ route('userView',$request->userDetail ? $request->userDetail->slug : '')}}">
                                                    {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                                    </a>
                                                @else
                                                    <a style="color:#222" href="#">
                                                    {!! $request->userDetail ? $request->userDetail->firstname.' '.$request->userDetail->lastname : '' !!}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                 <a style="color:#222" href="{{ route('driverDetails',$request->driverDetail ? $request->driverDetail->slug : '')}}">
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
                                                    <span class="badge badge-warning">{{ __('trip_started') }}</span>
                                                @elseif($request->is_driver_arrived == 1)
                                                    <span class="badge badge-warning">{{ __('driver_arrived') }}</span>
                                                @elseif($request->is_driver_started == 1)
                                                    <span class="badge badge-primary">{{ __('driver_accepted') }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ __('trip_created') }}</span>
                                                @endif
                                            </td>  
                                            <td>      
                                                <div class="btn-group">  
                                                @if(auth()->user()->can('dispatcherrequest-view'))
                                                @if($request->driver_id != NULL || $request->is_driver_started == 1 || $request->is_cancelled == 1)
                                                    <a href="{{ route('dispatchRequestView',$request->id) }}" class="btn bg-purple-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="View Trip"> <i class="icon-eye"></i> </a>
                                                @endif      
                                                @endif      
                                                @if(auth()->user()->can('dispatcher-edit'))
                                                @if($request->is_driver_started == 0 && $request->is_cancelled == 0)
                                                    <a href="{{ route('dispatcherEdit',$request->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit Trip"> <i class="icon-pencil5"></i> </a>
                                                @endif   
                                                @endif   
                                                @if(auth()->user()->can('dispatcher-delete'))
                                                
                                                    @if($request->is_completed == 0 && $request->is_cancelled == 0)
                                                    <a href="#" onclick="Javascript: return deleteAction('$documents->slug', `{{ route('dispatchTripCancel',$request->id) }}`)" class="btn bg-danger-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Cancel Trip"> <i class="icon-x"></i> </a>
                                                    @endif
                                                @endif 
                                                </div>
                                            </td>
                    
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
<!-- /horizontal form modal -->

@endsection
