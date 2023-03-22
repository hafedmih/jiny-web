@extends('layouts.app')

@section('content')
<div class="content-wrapper">
	<!-- Inner content -->
	<div class="content-inner">
		<!-- Content area -->
		<div class="content">
			<div class="card">
				<div class="card-header header-elements-inline">
					<h5 class="card-title">{{ __('manage-wallet') }} - {{$user->firstname}} {{$user->lastname}}</h5>
					<div class="header-elements">
						<div class="list-icons">
					<button type="button" class="btn btn-light" data-toggle="modal" data-target="#modal_form_inline">Add Amount <i class="icon-plus2"></i></button>									</div>
					</div>
				</div>
			</div>
			<!-- Dashboard content -->
			<div class="row">
				<div class="col-xl-8">
					<!-- Marketing campaigns -->
					<div class="card">
						<div class="card-header header-elements-sm-inline">
							<h6 class="card-title">{{ __('manage-wallet') }} </h6><span style="float:right !important;"><b>Last updated </b> - {{ $wallet->updated_at}}</span>
						</div>
						<!-- <div class="card-body d-sm-flex align-items-sm-center justify-content-sm-between flex-sm-wrap">
							
							<div class="d-flex align-items-center mb-3 mb-sm-0">
								<div id="campaigns-donut"><svg width="42" height="42"><g transform="translate(21,21)"><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(102, 187, 106);" d="M1.1634144591899855e-15,19A19,19 0 0,1 -14.050144241469582,12.790365389381929L-7.025072120734791,6.3951826946909645A9.5,9.5 0 0,0 5.817072295949927e-16,9.5Z"></path></g><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(149, 117, 205);" d="M-14.050144241469582,12.790365389381929A19,19 0 0,1 0.6493373977393208,-18.988900993577726L0.3246686988696604,-9.494450496788863A9.5,9.5 0 0,0 -7.025072120734791,6.3951826946909645Z" transform="translate(0,0)"></path></g><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(255, 112, 67);" d="M0.6493373977393208,-18.988900993577726A19,19 0 0,1 5.817072295949928e-15,19L2.908536147974964e-15,9.5A9.5,9.5 0 0,0 0.3246686988696604,-9.494450496788863Z"></path></g></g></svg></div>
								<div class="ml-3">
									<h5 class="font-weight-semibold mb-0">{{ $wallet->balance_amount}}</h5>
									<span class="badge badge-mark border-success mr-1"></span> <span class="text-muted">Wallet Balance Amount</span>
								</div>
							</div>

							<div class="d-flex align-items-center mb-3 mb-sm-0">
								<div id="campaign-status-pie"><svg width="42" height="42"><g transform="translate(21,21)"><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(41, 182, 246);" d="M1.1634144591899855e-15,19A19,19 0 0,1 -10.035763324841723,-16.133302652828462L-5.017881662420861,-8.066651326414231A9.5,9.5 0 0,0 5.817072295949927e-16,9.5Z"></path></g><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(239, 83, 80);" d="M-10.035763324841723,-16.133302652828462A19,19 0 0,1 17.35205039879773,-7.739919053684189L8.676025199398865,-3.8699595268420945A9.5,9.5 0 0,0 -5.017881662420861,-8.066651326414231Z"></path></g><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(129, 199, 132);" d="M17.35205039879773,-7.739919053684189A19,19 0 0,1 14.540850859600345,12.229622082421841L7.270425429800173,6.1148110412109205A9.5,9.5 0 0,0 8.676025199398865,-3.8699595268420945Z"></path></g><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(153, 153, 153);" d="M14.540850859600345,12.229622082421841A19,19 0 0,1 5.817072295949928e-15,19L2.908536147974964e-15,9.5A9.5,9.5 0 0,0 7.270425429800173,6.1148110412109205Z"></path></g></g></svg></div>
								<div class="ml-3">
									<h5 class="font-weight-semibold mb-0">{{ $wallet->earned_amount}}</h5>
									<span class="badge badge-mark border-success mr-1"></span> <span class="text-muted">Wallet Earned Amount</span>
								</div>
							</div>

							<div class="d-flex align-items-center mb-3 mb-sm-0">
								<div id="campaign-status-pie"><svg width="42" height="42"><g transform="translate(21,21)"><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(41, 182, 246);" d="M1.1634144591899855e-15,19A19,19 0 0,1 -10.035763324841723,-16.133302652828462L-5.017881662420861,-8.066651326414231A9.5,9.5 0 0,0 5.817072295949927e-16,9.5Z"></path></g><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(239, 83, 80);" d="M-10.035763324841723,-16.133302652828462A19,19 0 0,1 17.35205039879773,-7.739919053684189L8.676025199398865,-3.8699595268420945A9.5,9.5 0 0,0 -5.017881662420861,-8.066651326414231Z"></path></g><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(129, 199, 132);" d="M17.35205039879773,-7.739919053684189A19,19 0 0,1 14.540850859600345,12.229622082421841L7.270425429800173,6.1148110412109205A9.5,9.5 0 0,0 8.676025199398865,-3.8699595268420945Z"></path></g><g class="d3-arc d3-slice-border" style="cursor: pointer;"><path style="fill: rgb(153, 153, 153);" d="M14.540850859600345,12.229622082421841A19,19 0 0,1 5.817072295949928e-15,19L2.908536147974964e-15,9.5A9.5,9.5 0 0,0 7.270425429800173,6.1148110412109205Z"></path></g></g></svg></div>
								<div class="ml-3">
									<h5 class="font-weight-semibold mb-0">{{ $wallet->amount_spent}}</h5>
									<span class="badge badge-mark border-success mr-1"></span> <span class="text-muted">Wallet Spent Amount</span>
								</div>
							</div>
						</div> -->
						<div class="card-body ">
							<div class="row">
								<div class="col-sm-6 col-xl-4">
									<div class="card card-body bg-blue-400 has-bg-image">
										<div class="media mb-3">
											<div class="ml-3">
											<div class="d-flex">
												<h3 class="font-weight-semibold  mb-0 " >{{$currency}} {{ $wallet->balance_amount}}</h3>
											</div>
											<span class="text">{{ __('wallet _balance _amount')}}</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6 col-xl-4">
									<div class="card card-body bg-danger-400 has-bg-image">
										<div class="media mb-3">
											<div class="ml-3">
												<div class="d-flex">
													<h3 class="font-weight-semibold mb-0">{{$currency}} {{ $wallet->amount_spent}}</h3>
												</div>
												<span class="text" >{{ __('wallet_spent_amount')}}</span>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6 col-xl-4">
									<div class="card card-body bg-success-400 has-bg-image">
										<div class="media mb-3">
											<div class="ml-3">
											<div class="d-flex">
												<h3 class="font-weight-semibold mb-0">{{$currency}} {{ $wallet->earned_amount}}</h3>
											</div>
												</span> <span class="text">{{ __('wallet_earned_amount')}}</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="table-responsive">
							<table class="table text-nowrap">
								<thead>
									<tr>
										<th>{{ __('sl')}}</th> 
										<th>{{ __('amount')}}</th>
										<th>{{ __('purpose')}}</th>
										<th>{{ __('type')}}</th>
										<th>{{ __('status')}}</th>
										<th>{{ __('request_id')}}</th>
									</tr>
								</thead>
								<tbody>
									<tr class="table-active table-border-double">
										<td colspan="6">{{ __('wallet_transaction_history')}}</td>
									</tr>
										@foreach($wallet_transaction as $key => $transaction)
									<tr>
										<td>
											<div class="d-flex align-items-center">
												<div>
													<a href="#" class="text-body font-weight-semibold">{{ ++$key }}</a>
												</div>
											</div>
										</td>
										<td>
											@if($transaction->type == "EARNED")
											<span class="text-success"><i class="icon-stats-growth2 mr-2"></i> {{ $transaction->amount}}</span>
											@elseif($transaction->type == "SPENT")
											<span class="text-danger"><i class="icon-stats-decline2 mr-2"></i> {{ $transaction->amount}}</span>
											@endif
										</td>
										<td><span class="text-muted">{{ $transaction->purpose}}</span></td>
										<td><h6 class="font-weight-semibold mb-0" style="text-transform: capitalize;">{{ $transaction->type}}</h6></td>
										<td class="text-center">
											Success
										</td>
										<td>
										@if(auth()->user()->can('request-view'))
											<a href="{{ route('requestView',$transaction->request_id) }}" style="color:#333"> 
											{{$transaction->getRequest ? $transaction->getRequest->request_number : ''}} </a>
										@endif 
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					<!-- /marketing campaigns -->
				</div>

				<div class="col-xl-4">
					<!-- Progress counters -->
					<!-- <div class="row">
						<div class="col-sm-6">

							<div class="card text-center">
								<div class="card-body">
									<div class="svg-center position-relative" id="hours-available-progress"></div>
									<div id="hours-available-bars1"></div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="card text-center">
								<div class="card-body">
									<div class="svg-center position-relative" id="hours-available-progress1"></div>
									<div id="hours-available-bars2"></div>
								</div>
							</div>
						</div>
					</div> -->
					<!-- /progress counters -->
				</div>
			</div>
			<!-- /dashboard content -->
		</div>
		<!-- /content area -->




			<!-- Horizontal form modal -->
		<div id="roleModel" class="modal fade" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<h5 class="modal-title " id="modelHeading">{{ __('add-amount') }}</h5>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<form id="roleForm" name="roleForm" class="form-horizontal">
						@csrf

						<div class="modal-body">
							<div class="alert alert-danger alert-dismissible" id="errorbox">
								<!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
								<span id="errorContent"></span>
							</div>
							<div class="form-group row">
								<label class="col-form-label col-sm-3">{{ __('phone-number') }}</label>
								<div class="col-sm-9">
									<input type="text" name="amount" id="amounts" class="form-control"  placeholder="Amount">
									<input type="hidden" name="sos_id" id="sos_id">
								</div>
							</div>
							

						<div class="modal-footer">
							<button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
							<button type="submit" id="saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
						</div>
					</form>
				</div>
			</div>
		</div>

	</div>
  <!-- Inline form modal -->
	<div id="modal_form_inline" class="modal fade" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add Amount</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<form action="#" class="modal-body form-inline justify-content-center">
					<label>Amount:</label>
					<input type="text" placeholder="Amount" name="amount" id="amount" class="form-control mb-2 mr-sm-2 ml-sm-2 mb-sm-0">
					<input type="hidden" name="user_id" id="user_id" value="{{$user->slug}}" >

					<button type="button" class="btn bg-primary ml-sm-2 mb-sm-0" id="add_amount">Add Amount <i class="icon-plus22"></i></button>
				</form>
			</div>
		</div>
	</div>
	<!-- /inline form modal -->
</div>
@include('layouts.scripts')
<script>
    _ProgressRoundedChart('#hours-available-progress', 38, 2, '#F06292', 0.{{ $wallet->earned_amount}}, 'icon-cash text-pink-400', 'Earned Amount', '');
    _ProgressRoundedChart('#hours-available-progress1', 38, 2, '#42a5f5', 0.{{ $wallet->amount_spent}}, 'icon-cash text-primary-400', 'Spent Amount', '');
	_BarChart('#hours-available-bars1', "{{implode(',',$earn_wallet_transaction)}}", 40, true, 'elastic', 1300, 50, '#EC407A', 'hours');
	_BarChart('#hours-available-bars2', "{{implode(',',$spent_wallet_transaction)}}", 40, true, 'elastic', 1300, 50, '#42a5f5', 'hours');
</script>

<script>

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $(document).on('click','#add_amount',function(){
        var amount = $("#amount").val();
        var user_id = $("#user_id").val();
        
        var formData = new FormData();
        formData.append('amount',amount);
        formData.append('user_id',user_id);

        $.ajax({
            data: formData,
            url: "{{ route('walletSaveAmount') }}",
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
                    window.location.href = "{{url('user-wallet')}}/"+user_id;
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
