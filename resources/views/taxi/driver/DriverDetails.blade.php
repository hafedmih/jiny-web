@extends('layouts.app')

@section('content')

    <style>
        .list-group-item{
            padding: 0.55rem 0.25rem !important;
        }    
    </style>

    <div class="content">

        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('manage-driver-trips') }} </h5>
                <div class="header-elements">
                    <div class="list-icons">
                    {{ __('name') }} : {{$user->firstname}} {{$user->lastname}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-6 col-xl-4">
                <div class="card bg-green-400 has-bg-image">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="mb-0">{{$user->driverRequestDetail ? count($user->driverRequestDetail) : '0'}}</h3>
                                <span class="text-uppercase font-size-xs">{{ __('total_trips') }}</span>
                            </div>

                            <div class="ml-3 align-self-center">
                                <i class="icon-car2 icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex">
                        <a href="{{ route('driverTripDetails',$user->slug) }}" class="ml-auto text-white">Read more <i class="icon-arrow-right14 ml-2"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
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
                        <a href="{{ route('driverComplaintsList',$user->slug) }}" class="ml-auto text-white">Read more <i class="icon-arrow-right14 ml-2"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
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
                        <a href="{{ route('driverRatingsList',$user->slug) }}" class="ml-auto text-white">Read more <i class="icon-arrow-right14 ml-2"></i></a>
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
                            <a href="{{ route('driverTripDetails',$user->slug) }}" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    @foreach($user->driverRequest as $key => $value)               
                        <div class="col-lg-6">
                            <div class="card border-left-3 border-left-danger rounded-left-0">
                                <div class="card-body">
                                    <div class="d-sm-flex align-item-sm-center flex-sm-nowrap">
                                        <div>
                                            <h6 class="font-weight-semibold">{{$value->userDetail ? $value->userDetail->firstname : ''}} {{$value->userDetail ? $value->userDetail->lastname : ''}}</h6>
                                            <ul class="list list-unstyled mb-0"> 
                                                <li>{{ __('invoice')}} #:<a style="color:#222" href="{{ route('requestView',$value->id) }}"> {{$value->request_number}}</a></li>
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

                 <div class="row">
                    <div class="col-sm-7 col-xl-6">
                        <div class="card ">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('questions')}}</th>
                                            <th>{{ __('up_percentage')}}</th>
                                            <th>{{ __('down_percentage')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($questions as $key => $value)
                                        <tr>
                                            <td>{!!$value->questions !!}</td>
                                            <td><i class="icon-thumbs-up3 text-success ml-auto"></i>{!!round($value->up_percentage, 1); !!}%</td>
                                            <td><i class="icon-thumbs-down3 text-danger ml-auto"></i>{!!round($value->down_percentage,1); !!}%</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                   
                </div>


                
                    
            </div>
           

            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-img-actions d-inline-block mb-3">
                            <img class="img-fluid rounded-circle" src="@if($user->profile_pic)  @else {{ asset('backend/global_assets/images/demo/users/face6.jpg') }} @endif" width="170" height="170" alt="">
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
                            
                            <li class="list-group-item">
                                <span class="font-weight-semibold">
                                    <i class="icon-car2 mr-2"></i>								
                                    {{ $user->driver ? $user->driver->vehicletype->vehicle_name : ''}}                              
                                </span>
                            </li>
                            
                            <li class="list-group-item">
                                <span class="font-weight-semibold">
                                    <i class="icon-bookmark2 mr-2"></i>
                                    {{ $user->driver ? $user->driver->subscription_type : '' }}
                                </span>
                            </li>

                            
                        </ul>
                        
                       
                        <!-- <h4 class="font-weight-semibold mb-0">{{ $user->DriversLog ? $user->DriversLog->working_time : '' }}</h4> -->
                        <h4 class="font-weight-semibold mb-0">{{$drivers_online->today_working}}</h4>
                        <h6 class="font-weight-semibold mb-0">Today Working</h6> 
       

                        <div class="d-block opacity-75"><span class="badge badge-mark border-success mr-1"></span>Online Time : {{ $user->DriversLog ? date("H:i:s",strtotime($user->DriversLog->online_time)) : '' }}</div>
                       
                        @if($user->DriversLog ? $user->DriversLog->offline_time: '' )
                        <div class="d-block opacity-75"><span class="badge badge-mark border-danger mr-1"></span>Offline Time : {{ $user->DriversLog ? date("H:i:s",strtotime($user->DriversLog->offline_time)) : '' }}</div>
                        @endif
                        <div class="d-flex">
                            <a href="{{ route('driverWorkingHours',$user->slug) }}" class="ml-auto text-dark">Read more <i class="icon-arrow-right14 ml-2"></i></a>
                        </div>
                    </div>
                </div>
                @if( $user->driver ? $user->driver->subscription_type : '' &&  $user->driver->subscription_type != 'COMMISSION')
                    @if($subscription)
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h6 class="card-title">Subscription Details</h6>
                            <div class="header-elements">
                                <a href="#">View all</a> 
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="font-weight-semibold mb-0">{{$subscription->amount}}</h4>
                            <span class="d-block font-weight-semibold">{{$subscription->subscriptionDetails->name}}</span>
                            <div class="d-sm-flex flex-sm-wrap mb-3">
                                <div class="font-weight-semibold">From Date:<br>{{date("d-m-Y",strtotime($subscription->from_date))}}</div>
                                <div class="ml-sm-auto mt-2 mt-sm-0 font-weight-semibold">TO Date:<br>{{date("d-m-Y",strtotime($subscription->to_date))}}</div>
                            </div>
                            <div class="d-sm-flex flex-sm-wrap mb-3">
                                <div class="font-weight-semibold">Total days : {{$subscription->subscriptionDetails->validity}}</div>
                                <div class="ml-sm-auto mt-2 mt-sm-0 font-weight-semibold">Balance days : {{$subscription->balance_days}}</div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="icon-cross icon-2x text-danger border-danger border-3 rounded-pill p-2 mb-3 mt-1"></i>
                            <h5 class="card-title">No Subscription</h5>
                            <!-- <p class="mb-3">Ouch found swore much dear conductively hid submissively hatchet vexed far</p> -->
                            <!-- <a href="#" class="btn btn-success">Browse articles</a> -->
                        </div>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        
                
        
                   
                    
                
            
        
        <div class="row">

            <div class="col-sm-3 card card-body">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <a href="{{ route('userWallet',$user->slug) }}" class="btn bg-transparent border-warning text-warning rounded-pill border-2 btn-icon mr-3">
                                <i class="icon-wallet icon-1x opacity-75"></i>
                    </a>
                    
                    <div>
                        <div class="font-weight-semibold">Wallet</div>
                        <span class="text-muted">{{$user->wallet ? $user->wallet :'0' }}</span>
                    </div>
                    </a>
                </div>
            </div>
            
            <div class="col-sm-3 card card-body">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <a href="#" class="btn bg-transparent border-green text-warning rounded-pill border-2 btn-icon mr-3">
                        <i class="icon-plus3" style="color:green;"></i>
                    </a>
                    <div>
                        <div class="font-weight-semibold">Bonus</div>
                        <span class="text-muted">{{$user->bonus_amount}}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 card card-body">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <a href="{{ route('driverRefernceList',$user->slug) }}" class="btn bg-transparent border-indigo text-indigo rounded-pill border-2 btn-icon mr-3">
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
                    <a href="{{ route('driverFineList',$user->slug) }}" class="btn bg-transparent border-danger text-teal rounded-pill border-2 btn-icon mr-3">
                        <i class="icon-minus3" style="color:#f44336;"></i>
                    </a>
                    <div> 
                        <div class="font-weight-semibold">Fine</div>
                        <span class="text-muted">{{$user->fine_amount}}</span>
                    </div>
                </div>
            </div>


        </div>

        

    </div>
    <!-- /horizontal form modal -->

    <script type="text/javascript">
        var i =1;
        var approved = [];
        var denaited = [];
        $(".dated").hide();
        $(".hide").hide();

        function updateAction(actionUrl){
            $.ajax({
                url: actionUrl,
                type: "GET",
                dataType: 'json',
                success: function (data) {
                    $('#roleForm').trigger("reset");
                console.log(data);
                    $('#modelHeading').html("{{ __('edit-document') }}");
                    $('#errorbox').hide();
                    // $('#saveBtn').html("Edit Complaint");
                    $('#saveBtn').val("edit_document");
                    $('#roleModel').modal('show');
                    $('#document_id').val(data.data.slug);
                    $('#title').val(data.data.document_name);
                    $('#date_required').val(data.data.expiry_date);
                    $('#image').attr("src",data.data.document_image);
                    if(data.data.expiry_date == 1){
                        $(".dated").show();
                        $('.date_lable').html("{{ __('experie-date') }}");
                        $('#expiry_date').val(data.data.expiry_dated);
                    }
                    else if(data.data.expiry_date == 2){
                        $(".dated").show();
                        $('.date_lable').html("{{ __('issue-date') }}");
                        $('#expiry_date').val(data.data.issue_dated);
                    }
                    else{
                        $(".dated").hide();
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            return false;
        }

        $('#document_image').change(function(){
            let reader = new FileReader();
            reader.onload = (e) => { 
                $("#image").attr('src',e.target.result);
            }
            reader.readAsDataURL(this.files[0]); 
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".approved").click(function(){
            var id = $(this).attr('for');
            if(approved.indexOf($("#"+id).val()) == -1){
                approved = [];
                $(this).removeClass('btn-outline');
                $("#"+id).prop('checked', true);
                $('input[name="approved[]"]:checkbox:checked').each(function(i){
                approved[i] = $(this).val();
                });
                console.log(approved);
            }
            else{
                approved = [];
                $(this).addClass('btn-outline');
                $("#"+id).prop('checked', false);
                $('input[name="approved[]"]:checkbox:checked').each(function(i){
                approved[i] = $(this).val();
                });
                console.log(approved);
            }
        });

        $(".denaited").click(function(){
            var id = $(this).attr('for');
            if(denaited.indexOf($("#"+id).val()) == -1){
                denaited = [];
                $(this).removeClass('btn-outline');
                $("#"+id).prop('checked', true);
                $('input[name="denaited[]"]:checkbox:checked').each(function(i){
                denaited[i] = $(this).val();
                });
                console.log(denaited);
            }
            else{
                denaited = [];
                $(this).addClass('btn-outline');
                $("#"+id).prop('checked', false);
                $('input[name="denaited[]"]:checkbox:checked').each(function(i){
                denaited[i] = $(this).val();
                });
                console.log(denaited);
            }
        });

        $('#saveBtn').click(function (e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append('document_image',$('#document_image').prop('files').length > 0 ? $('#document_image').prop('files')[0] : '');
            formData.append('document_id',$('#document_id').val());
            formData.append('driver_id',$('#driver_id').val());
            formData.append('date_required',$('#date_required').val());
            formData.append('expiry_date',$('#expiry_date').val());
            $(this).html("{{ __('sending') }}");
            $("#errorbox").hide();
            $.ajax({
                data: formData,
                url: "{{ route('driverDocumentUpdate') }}",
                type: "POST",
                dataType: 'json',
                contentType : false,
                processData: false,
                success: function (data) {
                    swal({
                        title: "{{ __('data-added') }}",
                        text: "{{ __('data-added-successfully') }}",
                        icon: "success",
                    }).then((value) => {        
                        window.location.href = "../driver-document/"+$('#driver_id').val();
                    });
                            
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    $('#errorContent').html('');
                    $.each(err.errors, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtn').html("{{ __('save-changes') }}");
                }
            });
        });



        $('#upproved').click(function (e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append('approved_document_id',approved);
            formData.append('denaited_document_id',denaited);
            formData.append('driver_id',$('#driver_id').val());
            $.ajax({
                data: formData,
                url: "{{ route('driverDocumentApproved') }}",
                type: "POST",
                dataType: 'json',
                contentType : false,
                processData: false,
                success: function (data) {
                    swal({
                        title: "{{ __('data-added') }}",
                        text: "{{ __('data-added-successfully') }}",
                        icon: "success",
                    }).then((value) => {        
                        window.location.href = "{{ route('driver') }}";
                    });
                            
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    $('#errorContent').html('');
                    $.each(err.errors, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtn').html("{{ __('save-changes') }}");
                }
            });
        });
    </script>

@endsection
