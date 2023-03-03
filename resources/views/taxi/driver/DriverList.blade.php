@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>


<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-driver') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-driver'))
                        <a href="{{ route('driverAdd') }}" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
 
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Driver list</h6>
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
                        @if(auth()->user()->can('active-drivers'))
                          <li class="nav-item "><a href="#right-icon-tab1" class="nav-link active " data-toggle="tab"><i class="icon-user-check ml-2 text-success-800"></i><span class="text-success-400"> Active </span></a></li>
                        @endif
                        @if(auth()->user()->can('inactive-drivers'))
                          <li class="nav-item"><a href="#right-icon-tab2" class="nav-link " data-toggle="tab"><i class="icon-user-cancel ml-2 text-danger-800"></i> <span class="text-danger-800"> Inactive </span> </a></li>
                        @endif
                        @if(auth()->user()->can('block-drivers'))
                          <li class="nav-item"><a href="#right-icon-tab3" class="nav-link " data-toggle="tab"><i class="icon-user-block ml-2 text-dark-800"></i> <span class="text-dark-800"> Block </span> </a></li>
                        @endif
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="right-icon-tab1">
                            <div class="navbar navbar-expand-xl navbar-light bg-light navbar-component rounded">
                                <ul class="nav navbar-nav">
                                @php $i = 0; @endphp
                                @foreach($types as $value)
                                    <li class="nav-item mt-2 mb-2">
                                        <a><span style="font-weight: bold">{{$value}}</span>
                                            <span class="badge badge-pill badge-success position-static ml-0 mr-4">{!! $active_count[$i] !!}</span>
                                        </a>
                                    </li>
                                    @php $i++; @endphp
                                 @endforeach
                                </ul>
                            </div> 
                            <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('sl') }}</th>
                                        <th>{{ __('action') }}</th>
                                        <th>{{ __('details')}}</th>
                                        <th>{{ __('wallet')}}</th>
                                        <th>{{ __('rating')}}</th>
                                        <!-- <th>{{ __('acceptance_ratio') }}</th> -->
                                        <!-- <th>{{__('brand_label')}}</th> -->
                                        <th>{{__('vehicle_type')}}</th>
                                        <th>{{__('document_uploaded')}}</th>
                                        <!-- <th>{{__('sign_up_date')}}</th>
                                        <th>{{__('notes')}}</th>
                                        <th>{{__('approved_by')}}</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($drivers_active as $key => $driver)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td >
                                                <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                                
                                                    @if(auth()->user()->can('view-driver'))
                                                        <a href="{{ route('driverDetails',$driver->slug) }}" class="dropdown-item"><i class="icon-eye"></i> View Driver</a>
                                                    @endif

                                                    @if(auth()->user()->can('edit-driver'))
                                                        <a href="{{ route('driverEdit',$driver->slug) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit Details</a>
                                                    @endif
                                                    @if(auth()->user()->can('active-driver'))
                                                        @if($driver->active == 1)
                                                            <a href="#" onclick="Javascript: return activeAction(`{{ route('driverActive',$driver->slug) }}`)" class="dropdown-item"><i class="icon-user-block"></i>Block Driver</a>
                                                        @else
                                                            <a href="#"  onclick="Javascript: return activeAction(`{{ route('driverActive',$driver->slug) }}`)" class="dropdown-item"><i class="icon-user-check"></i>Unblock driver</a>
                                                        @endif
                                                    @endif
                                                    @if(auth()->user()->can('delete-driver'))
                                                        <div class="dropdown-divider"></div>
                                                        <a href="#" onclick="Javascript: return deleteAction('$driver->slug', `{{ route('driverDelete',$driver->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i>{{ __('delete') }}</a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        @if($driver->profile_pic == NUll)
                                                            <a href="#" class="btn bg-violet rounded-round btn-icon btn-sm legitRipple">
                                                                <span class="letter-icon">R</span>
                                                            </a>
                                                        @else
                                                            <a href="#" >
                                                                <img src="{{$driver->profile_pic}}" class="rounded-circle" width="32" height="32" alt="">
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('driverDetails',$driver->slug)}}" class="text-default font-weight-semibold letter-icon-title">{!! $driver->firstname !!} {!! $driver->lastname !!}
                                                            <br>
                                                            {!! $driver->phone_number !!}
                                                        </a>
                                                        @if($driver->online_by)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> Online</div>
                                                        @else
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> Offline</div>
                                                        @endif
                                                    </div>
                                                </div>                                            
                                            </td>
                                            <td>{!! $driver->getCountry ? $driver->getCountry->currency_symbol : '' !!} {!! $driver->wallet_balance !!} </td>
                                            <td>
                                                
                                                {{ $driver->rating }} <i style="color:#fdde00" class="icon-star-full2"></i>
                                            </td>
                                            <!-- <td>
                                               
                                                @if($driver->driver &&  $driver->driver->acceptance_ratio > 50)
                                                    <span class="text-success-600"><i class="icon-stats-growth2 mr-2"></i> {{$driver->driver->acceptance_ratio}}%</span>
                                                @else
                                                    <span class="text-danger"><i class="icon-stats-decline2 mr-2"></i> {{$driver->driver && $driver->driver->acceptance_ratio}}%</span>
                                                @endif -->
                                                <!-- <div class="progress rounded-round">
								                    <div class="progress-bar bg-success" style="width: {{$driver->acceptance_ratio}}%">
									                    <span>{{$driver->acceptance_ratio}}% Complete</span>
								                    </div>
							                    </div> -->
                                                
                                            <!-- </td>  -->
                                            <!-- <td>{!! $driver->driver ? $driver->driver->brand_label : '' !!} </td> -->
                                            <td>
                                                <div class="text-default font-weight-bold letter-icon-title">
                                                   {{$driver->driver ? $driver->driver->vehicletype->vehicle_name : ''}}                                                    
                                                </div>
                                                <div>
                                                    {{ $driver->driver ? $driver->driver->car_model : ''}}
                                                </div>
                                                

                                            </td>
                                            <td>
                                                  
                                            {!! $driver->documents !!}
                                            </td>
                                            <!-- <td>{!! $driver->created_at->isoFormat('MMM Do YYYY') !!}</td> -->
                                            <!-- <td>{!! $driver->driver ? $driver->driver->notes : '' !!}</td>
                                            <td>{!! $driver->driver ? $driver->driver->getApprover ? $driver->driver->getApprover->firstname.' '.$driver->driver->getApprover->lastname : '' : '' !!}</td> -->
                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade " id="right-icon-tab2">
                            <div class="navbar navbar-expand-xl navbar-light bg-light navbar-component rounded">
                                <ul class="nav navbar-nav">
                                @php $i = 0; @endphp
                                @foreach($types as $value)
                                    <li class="nav-item mt-2 mb-2">
                                        <a><span style="font-weight: bold">{{$value}}</span>
                                            <span class="badge badge-pill badge-success position-static ml-0 mr-4">{!! $count[$i] !!}</span>
                                        </a>
                                    </li>
                                    @php $i++; @endphp
                                 @endforeach
                                </ul>
                            </div>                    
                            <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('sl') }}</th>
                                        <th>{{ __('action') }}</th>
                                        <th>{{__('details')}}</th>
                                        <!-- <th>{{__('first-name')}}</th>
                                        <th>{{__('last-name')}}</th>
                                        <th>{{__('phone-number')}}</th> -->
                                        <!-- <th>{{ __('Wallet') }}</th> -->
                                        <!-- <th>Rating</th> -->
                                        <!-- <th>{{ __('acceptance_ratio') }}</th> -->
                                        <th>{{ __('block_reson') }}</th>
                                        <!-- <th>{{__('brand_label')}}</th> -->
                                        <th>{{__('vehicle_type')}}</th>
                                        <th>{{__('document_uploaded')}}</th>
                                        <!-- <th>{{__('sign_up_date')}}</th>
                                        <th>{{__('notes')}}</th> -->
                                        <!-- <th>{{__('Approved_by')}}</th> -->
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($drivers_incative as $key => $driver)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>   
                                                <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                                    @if(auth()->user()->can('view-driver'))
                                                        <a href="{{ route('driverDetails',$driver->slug) }}" class="dropdown-item"><i class="icon-eye"></i> View Driver</a>
                                                    @endif
                                                    @if(auth()->user()->can('edit-driver'))
                                                        <a href="{{ route('driverEdit',$driver->slug) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit Details</a>
                                                    @endif
                                                    @if(auth()->user()->can('active-driver'))
                                                        @if($driver->active == 1)
                                                            <a href="#" onclick="Javascript: return activeAction(`{{ route('driverActive',$driver->slug) }}`)" class="dropdown-item"><i class="icon-user-block"></i>Block Driver</a>
                                                        @else
                                                            <a href="#"  onclick="Javascript: return activeAction(`{{ route('driverActive',$driver->slug) }}`)" class="dropdown-item"><i class="icon-user-check"></i>Unblock driver</a>
                                                        @endif
                                                    @endif
                                                    @if(auth()->user()->can('delete-driver'))
                                                        <div class="dropdown-divider"></div>
                                                        <a href="#" onclick="Javascript: return deleteAction('$driver->slug', `{{ route('driverDelete',$driver->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i>{{ __('delete') }}</a>
                                                    @endif  
                                                </div>       
                                            </td>
                                            <!-- <td>
                                                <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            @if($driver->profile_pic == NUll)
                                                                <a href="#" class="btn bg-violet rounded-round btn-icon btn-sm legitRipple">
                                                                    <span class="letter-icon">R</span>
                                                                </a>
                                                            @else
                                                                <a href="#" >
                                                                    <img id="{{$driver->id}}" class="rounded-circle" width="32" height="32" alt="">
                                                                </a>
                                                            @endif
                                                        </div>
                                                        <a style="color:#222" href="{{ route('driverDetails',$driver->slug)}}">
                                                        {!! $driver->firstname !!}
                                                        </a>
                                                </div>        
                                            </td>
                                            <td>{!! $driver->lastname !!}</td>
                                            <td>{!! $driver->phone_number !!}</td> -->
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        @if($driver->profile_pic == NUll)
                                                            <a href="#" class="btn bg-violet rounded-round btn-icon btn-sm legitRipple">
                                                                <span class="letter-icon">R</span>
                                                            </a>
                                                        @else
                                                            <a href="#" >
                                                                <img src="{{ $driver->id }}" class="rounded-circle" width="32" height="32" alt="">
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <a href="#" class="text-default font-weight-semibold letter-icon-title">{!! $driver->firstname !!} {!! $driver->lastname !!}
                                                            <br>
                                                            {!! $driver->phone_number !!}
                                                        </a>
                                                        @if($driver->online_by)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> Online</div>
                                                        @else
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> Offline</div>
                                                        @endif
                                                    </div>
                                                </div>                                            
                                            </td>
                                            <!-- <td>{!! $driver->getCountry ? $driver->getCountry->currency_symbol : '' !!} {!! $driver->wallet_balance !!} </td> -->
                                            <!-- <td>
                                                -->
                                                <!-- {{ $driver->rating }} <i style="color:#fdde00" class="icon-star-full2"></i> -->
                                            <!-- </td> --> 
                                            <!-- <td>
                                                @if($driver->driver && $driver->driver->acceptance_ratio > 50)
                                                    <span class="text-success-600"><i class="icon-stats-growth2 mr-2"></i> {{$driver->driver->acceptance_ratio}}%</span>
                                                @else
                                                    <span class="text-danger"><i class="icon-stats-decline2 mr-2"></i> {{$driver->driver && $driver->driver->acceptance_ratio}}%</span>
                                                    @endif -->
                                                <!-- <div class="progress rounded-round">
                                                    <div class="progress-bar bg-success" style="width: {{$driver->driver && $driver->driver->acceptance_ratio}}%">
                                                        <span>{{$driver->driver && $driver->driver->acceptance_ratio}}% Complete</span>
                                                    </div>
                                                </div> -->
                                            <!-- </td>  -->
                                            <td>{!! $driver->block_reson !!} </td>
                                            <!-- <td>{!! $driver->driver ? $driver->driver->brand_label : '' !!}</td> -->
                                            
                                            <td>
                                                <div class="text-default font-weight-bold letter-icon-title">
                                                   {{$driver->driver ? $driver->driver->vehicletype->vehicle_name : ''}}                                                    
                                                </div>
                                                <div>
                                                    {{ $driver->driver ? $driver->driver->car_model : ''}}
                                                </div>                                               

                                            </td>
                                                

                                            <td>
                                                {!! $driver->documents !!}
                                            </td>
                                            <!-- <td>{!! $driver->created_at->isoFormat('MMM Do YYYY') !!}</td>
                                            <td>{!! $driver->driver ? $driver->driver->notes : '' !!}</td> -->
                                            <!-- <td>{!! auth()->user()->email !!}</td> -->
                                             
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade " id="right-icon-tab3">
                            <div class="navbar navbar-expand-xl navbar-light bg-light navbar-component rounded">
                                <ul class="nav navbar-nav">
                                @php $i = 0; @endphp
                                @foreach($types as $value)
                                    <li class="nav-item mt-2 mb-2">
                                        <a><span style="font-weight: bold">{{$value}}</span>
                                            <span class="badge badge-pill badge-success position-static ml-0 mr-4">{!! $block_count[$i] !!}</span>
                                        </a>
                                    </li>
                                    @php $i++; @endphp
                                 @endforeach
                                </ul>
                            </div>                    
                            <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('sl') }}</th>
                                        <th>{{ __('action') }}</th>
                                        <th>{{__('details')}}</th>
                                        <!-- <th>{{__('first-name')}}</th>
                                        <th>{{__('last-name')}}</th>
                                        <th>{{__('phone-number')}}</th> -->
                                        <!-- <th>{{ __('Wallet') }}</th> -->
                                        <!-- <th>Rating</th> -->
                                        <!-- <th>{{ __('acceptance_ratio') }}</th> -->
                                        <th>{{ __('block_reson') }}</th>
                                        <!-- <th>{{__('brand_label')}}</th> -->
                                        <th>{{__('vehicle_type')}}</th>
                                        <th>{{__('document_uploaded')}}</th>
                                        <!-- <th>{{__('sign_up_date')}}</th>
                                        <th>{{__('notes')}}</th> -->
                                        <!-- <th>{{__('Approved_by')}}</th> -->
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($drivers_block as $key => $driver)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>   
                                                <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                                    @if(auth()->user()->can('view-driver'))
                                                        <a href="{{ route('driverDetails',$driver->slug) }}" class="dropdown-item"><i class="icon-eye"></i> View Driver</a>
                                                    @endif
                                                    @if(auth()->user()->can('edit-driver'))
                                                        <a href="{{ route('driverEdit',$driver->slug) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit Details</a>
                                                    @endif
                                                    @if(auth()->user()->can('active-driver'))
                                                        @if($driver->active == 1)
                                                            <a href="#" onclick="Javascript: return activeAction(`{{ route('driverActive',$driver->slug) }}`)" class="dropdown-item"><i class="icon-user-block"></i>Block Driver</a>
                                                        @else
                                                            <a href="#"  onclick="Javascript: return activeAction(`{{ route('driverActive',$driver->slug) }}`)" class="dropdown-item"><i class="icon-user-check"></i>Unblock driver</a>
                                                        @endif
                                                    @endif
                                                    @if(auth()->user()->can('delete-driver'))
                                                        <div class="dropdown-divider"></div>
                                                        <a href="#" onclick="Javascript: return deleteAction('$driver->slug', `{{ route('driverDelete',$driver->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i>{{ __('delete') }}</a>
                                                    @endif  
                                                </div>       
                                            </td>
                                            <!-- <td>
                                                <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            @if($driver->profile_pic == NUll)
                                                                <a href="#" class="btn bg-violet rounded-round btn-icon btn-sm legitRipple">
                                                                    <span class="letter-icon">R</span>
                                                                </a>
                                                            @else
                                                                <a href="#" >
                                                                    <img id="{{$driver->id}}" class="rounded-circle" width="32" height="32" alt="">
                                                                </a>
                                                            @endif
                                                        </div>
                                                        <a style="color:#222" href="{{ route('driverDetails',$driver->slug)}}">
                                                        {!! $driver->firstname !!}
                                                        </a>
                                                </div>        
                                            </td>
                                            <td>{!! $driver->lastname !!}</td>
                                            <td>{!! $driver->phone_number !!}</td> -->
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        @if($driver->profile_pic == NUll)
                                                            <a href="#" class="btn bg-violet rounded-round btn-icon btn-sm legitRipple">
                                                                <span class="letter-icon">R</span>
                                                            </a>
                                                        @else
                                                            <a href="#" >
                                                                <img src="{{ $driver->id }}" class="rounded-circle" width="32" height="32" alt="">
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <a href="#" class="text-default font-weight-semibold letter-icon-title">{!! $driver->firstname !!} {!! $driver->lastname !!}
                                                            <br>
                                                            {!! $driver->phone_number !!}
                                                        </a>
                                                        @if($driver->online_by)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> Online</div>
                                                        @else
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> Offline</div>
                                                        @endif
                                                    </div>
                                                </div>                                            
                                            </td>
                                            <!-- <td>{!! $driver->getCountry ? $driver->getCountry->currency_symbol : '' !!} {!! $driver->wallet_balance !!} </td> -->
                                            <!-- <td>
                                                 -->
                                                <!-- {{ $driver->rating }} <i style="color:#fdde00" class="icon-star-full2"></i> -->
                                            <!-- </td> --> 
                                            <!-- <td>
                                                @if($driver->driver && $driver->driver->acceptance_ratio > 50)
                                                    <span class="text-success-600"><i class="icon-stats-growth2 mr-2"></i> {{$driver->driver->acceptance_ratio}}%</span>
                                                @else
                                                    <span class="text-danger"><i class="icon-stats-decline2 mr-2"></i> {{$driver->driver && $driver->driver->acceptance_ratio}}%</span>
                                                    @endif -->
                                                <!-- <div class="progress rounded-round">
                                                    <div class="progress-bar bg-success" style="width: {{$driver->driver && $driver->driver->acceptance_ratio}}%">
                                                        <span>{{$driver->driver && $driver->driver->acceptance_ratio}}% Complete</span>
                                                    </div>
                                                </div> -->
                                            <!-- </td>  -->
                                            <td>{!! $driver->block_reson !!} </td>
                                            <!-- <td>{!! $driver->driver ? $driver->driver->brand_label : '' !!}</td> -->
                                            
                                            <td>
                                                <div class="text-default font-weight-bold letter-icon-title">
                                                   {{$driver->driver ? $driver->driver->vehicletype->vehicle_name : ''}}                                                    
                                                </div>
                                                <div>
                                                    {{ $driver->driver ? $driver->driver->car_model : ''}}
                                                </div>                                               

                                            </td>
                                                

                                            <td>
                                                {!! $driver->documents !!}
                                            </td>
                                            <!-- <td>{!! $driver->created_at->isoFormat('MMM Do YYYY') !!}</td>
                                            <td>{!! $driver->driver ? $driver->driver->notes : '' !!}</td> -->
                                            <!-- <td>{!! auth()->user()->email !!}</td> -->
                                             
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <span class="card-title font-weight-semibold">Driver Details</span>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row row-tile no-gutters shadow-0 border">
                        <div class="col-6">
                            
                            <button type="button" class="btn btn-light btn-block btn-float m-0 legitRipple">
                                <i class=" icon-2x">{{ $activecount }}</i>
                                <span> Active</span>
                            </button>

                            <button type="button" class="btn btn-light btn-block btn-float m-0 legitRipple">
                                <i class="text-blue-400 icon-2x">{{ $onlinecount }}</i>
                                <span>Online</span>
                            </button>
                        </div>
                        
                        <div class="col-6">
                            
                            <button type="button" class="btn btn-light btn-block btn-float m-0 legitRipple">
                                <i class="text-pink-400 icon-2x">{{ $blockcount }}</i>
                                <span>Blocked</span>
                            </button>

                            <button type="button" class="btn btn-light btn-block btn-float m-0 legitRipple">
                                <i class=" text-success-400 icon-2x">{{ $offlinecount }}</i>
                                <span>Offline</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div> -->


            <!-- <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <span class="card-title font-weight-semibold">Balance changes</span>
                    <div class="header-elements">
                        <span><i class="icon-arrow-down22 text-danger"></i> <span class="font-weight-semibold">- 29.4%</span></span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="canvas" height="280" width="600"></canvas>
                        
                    </div>
                </div>
            </div> -->
        <!-- </div> -->
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

        @foreach($drivers_active as $key => $driver)

        if("{{$driver->profile_pic}}" != ""){
       
          getUrlByFileName('{{$driver->profile_pic}}', mimes.jpeg).then(function(data) {
    
        $("#{{$driver->id}}").attr('src',data);
      });
      }
      @endforeach



      @foreach($drivers_incative as $key => $driver)

        if("{{$driver->profile_pic}}" != ""){
       
          getUrlByFileName('{{$driver->profile_pic}}', mimes.jpeg).then(function(data) {
    
        $("#{{$driver->id}}").attr('src',data);
      });
      }
      @endforeach


  </script>


@endsection
