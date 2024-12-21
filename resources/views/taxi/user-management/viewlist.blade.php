@extends('layouts.app')

@section('content')

<div class="content">
     
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-user') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                {{ __('name') }} : {{$user->firstname}} {{$user->lastname}}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-success-400 has-bg-image">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0">{{$user?->getCountry?->currency_symbol}} {{$user->wallet ? $user->wallet->balance_amount : '0'}}</h3>
                            <span class="text-uppercase font-size-xs">{{ __('wallet') }}</span>
                        </div>
                        <div class="ml-3 align-self-center">
                            <i class="icon-wallet icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex">
					<a href="{{ route('userWallet',$user->slug) }}" class="ml-auto text-white">Read more <i class="icon-arrow-right14 ml-2"></i></a>
				</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-blue-400 has-bg-image">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0">{{count($user->UserComplaintsList)}}</h3>
                            <span class="text-uppercase font-size-xs">{{ __('complaints') }}</span>
                        </div>
                        <div class="ml-3 align-self-center">
                            <i class="icon-user-minus icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex">
					<a href="{{ route('userComplaintsList',$user->slug) }}" class="ml-auto text-white">Read more <i class="icon-arrow-right14 ml-2"></i></a>
				</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-danger-400 has-bg-image">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0">{{$user->rating ? round($user->rating,1) : '0'}}</h3>
                            <span class="text-uppercase font-size-xs">{{ __('rating') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="icon-medal-star icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex">
					<a href="{{ route('userRatingsList',$user->slug) }}" class="ml-auto text-white">Read more <i class="icon-arrow-right14 ml-2"></i></a>
				</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card bg-indigo-400 has-bg-image">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0">{{$user->UserRequestDetail ? count($user->UserRequestDetail) : '0'}}</h3>
                            <span class="text-uppercase font-size-xs">{{ __('total_trips') }}</span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <i class="icon-car2 icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex">
					<a href="{{ route('userTripsList',$user->slug) }}" class="ml-auto text-white">Read more <i class="icon-arrow-right14 ml-2"></i></a>
				</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-9">
            <div class="card">
				<div class="card-header bg-transparent header-elements-inline py-0">
					<h6 class="card-title py-3">Request Trips</h6>
					<div class="header-elements">
						<a href="{{ route('userTripsList',$user->slug) }}" class="btn btn-primary">View All</a>
					</div>
				</div>
			</div>
            <div class="row">
                @foreach($user->UserRequestDetail as $key => $value)
				<div class="col-lg-6">
					<div class="card border-left-3 border-left-danger rounded-left-0">
						<div class="card-body">
							<div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
								<div>
									<h6 class="font-weight-semibold">{{$value->driverDetail ? $value->driverDetail->firstname : ''}} {{$value->driverDetail ? $value->driverDetail->lastname : ''}}</h6>
									<ul class="list list-unstyled mb-0">
										<li>Invoice #: {{$value->request_number}}</li>
										<li>Issued on: <span class="font-weight-semibold">{{date("d-m-Y",strtotime($value->created_at))}}</span></li>
									</ul>
								</div>

								<div class="text-sm-right mb-0 mt-3 mt-sm-0 ml-auto">
									<h6 class="font-weight-semibold">{{$value->requestBill ? $value->requestBill->requested_currency_symbol : ''}} {{$value->requestBill ? $value->requestBill->total_amount : '0.00'}}</h6>
									<ul class="list list-unstyled mb-0">
										<li>Ride Type:
                                            @if($value->is_later == 0) 
                                            <span class="font-weight-semibold">Ride Now</span>
                                            @else
                                            <span class="font-weight-semibold">Ride Later</span>
                                            @endif
                                        </li>
										<li class="dropdown">
											Status: &nbsp;
                                            @if($value->is_cancelled == 1)
                                                <label class="badge bg-danger-400 align-top">Cancelled</label>
                                            @elseif($value->is_completed == 1)
                                                <label class="badge bg-success-400 align-top">Completed</label>
                                            @elseif($value->is_trip_start == 1)
                                                <label class="badge bg-warning-400 align-top">Trip Started</label>
                                            @elseif($value->is_driver_arrived == 1)
                                                <label class="badge bg-info-400 align-top">Driver Arrived</label>
                                            @else
                                                <label class="badge bg-primary-400 align-top">Trip Created</label>
                                            @endif
											
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="card-footer d-sm-flex justify-content-sm-between align-items-sm-center">
							<span>
								<span class="badge badge-mark border-danger mr-2"></span>
								Due:
								<span class="font-weight-semibold">{{date("d-m-Y",strtotime($value->created_at))}}</span>
							</span>
							<ul class="list-inline list-inline-condensed mb-0 mt-2 mt-sm-0">
								<li class="list-inline-item">
									<a href="{{ route('requestView',$value->id) }}" class="text-default"><i class="icon-eye8"></i></a>
								</li>
							</ul>
						</div>
					</div>
				</div>
                @endforeach
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
				<div class="card-body text-center">
					<div class="card-img-actions d-inline-block mb-3">
					    <img class=" rounded-circle" src="@if($user->profile_pic) {{$user->profile_pic}} @else {{ asset('backend/global_assets/images/demo/users/face6.jpg') }} @endif" width="170" height="170" alt="">
                        <!-- <div class=" card-img rounded-circle"> -->
                            <!-- <a href="#" class="btn btn-outline bg-white text-white border-white border-2 btn-icon rounded-round legitRipple">
                                <i class="icon-plus3"></i>
                            </a> -->
                        <!-- </div> -->
					</div>
                    <ul class="list-group border-x-0 rounded-0 text-left">
						<li class="list-group-item">
							<span class="font-weight-semibold">
								<i class="icon-user mr-2"></i>
								{{ $user->firstname }} {{ $user->lastname }}
							</span>
						</li>
						@if($user->email != '')
						<li class="list-group-item">
							<span class="font-weight-semibold">
								<i class="icon-envelop5 mr-2"></i>
								{{ $user->email }}
							</span>
						</li>
						@endif
		    			<li class="list-group-item">
							<span class="font-weight-semibold">
								<i class="icon-phone-wave mr-2"></i>
								{{ $user->phone_number }}
							</span>
						</li>
					</ul>
					<!-- <h6 class="font-weight-semibold mb-0">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</h6>
			    	<span class="d-block opacity-75">{{ auth()->user()->email }}</span>
			    	<span class="d-block opacity-75">{{ auth()->user()->phone_number }}</span> -->
				</div>
			</div>
        </div>
    </div>
    
    <div class="row">
		<div class="col-sm-3 card card-body">
			<div class="d-flex align-items-center justify-content-center mb-2">
				<a href="{{ route('userFineList',$user->slug) }}" class="btn bg-transparent border-teal text-teal rounded-pill border-2 btn-icon mr-3">
					<i class="icon-plus3"></i>
				</a>
				<div>
					<div class="font-weight-semibold">Fine</div>
					<span class="text-muted">{{$user->fine_amount}}</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 card card-body">
			<div class="d-flex align-items-center justify-content-center mb-2">
				<a href="#" class="btn bg-transparent border-warning text-warning rounded-pill border-2 btn-icon mr-3">
					<i class="icon-watch2"></i>
				</a>
				<div>
					<div class="font-weight-semibold">Bonus</div>
					<span class="text-muted">{{$user->bonus_amount}}</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 card card-body">
			<div class="d-flex align-items-center justify-content-center mb-2">
				<a href="{{ route('userreferal',$user->slug) }}" class="btn bg-transparent border-indigo text-indigo rounded-pill border-2 btn-icon mr-3">
					<i class="icon-people"></i>
				</a>
				<div>
					<div class="font-weight-semibold">Referal</div>
					<span class="text-muted"> {{$user->referal_count}}</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 card card-body">
			<div class="d-flex align-items-center justify-content-center mb-2">
				<a href="#" class="btn bg-transparent border-indigo text-indigo rounded-pill border-2 btn-icon mr-3">
					<i class="icon-people"></i>
				</a>
				<div>
					<div class="font-weight-semibold">Refernce</div>
					<span class="text-muted"> {{$user->referal_amount}}</span>
				</div>
			</div>
		</div>
	</div>

</div>

@endsection
