<!DOCTYPE html>
<html lang="en">
<head>
  <title>Roda Taxi</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>  -->
    <style>
        .text-danger strong {
            color: #9f181c;
        }
        .row {
            width: 100%;
            display: flex;
        }
        .table-bordered>:not(caption)>*>* {
            border-width: 0 1px;
        }
        .col-md-6 {
            flex: 0 0 auto;
            width: 50%;
        }
        .col-md-8 {
            flex: 0 0 auto;
            width: 66.66666667%;
        }
        .col-md-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
        }
        h4 {
            font-size: 1rem;
        }
        .col-md-9 {
            flex: 0 0 auto;
        }
        .col-md-3 {
            flex: 0 0 auto;
            width: 25%;
        }
		.receipt-main {
			background: #ffffff none repeat scroll 0 0;
			border-bottom: 12px solid #333333;
			border-top: 12px solid #ffd60b;
			margin-top: 50px;
			margin-bottom: 50px;
			padding: 40px 30px !important;
			position: relative;
			box-shadow: 0 1px 21px #acacac;
			color: #333333;
			font-family: open sans;
		}
		.receipt-main p {
			color: #333333;
			font-family: open sans;
			line-height: 1.42857;
		}
		.receipt-footer h1 {
			font-size: 15px;
			font-weight: 400 !important;
			margin: 0 !important;
		}
		.receipt-main::after {
			background: #414143 none repeat scroll 0 0;
			content: "";
			height: 5px;
			left: 0;
			position: absolute;
			right: 0;
			top: -13px;
		}
		.receipt-main thead {
			background: #414143 none repeat scroll 0 0;
		}
		.receipt-main thead th {
			color:#fff;
		}
		.text-right {
            text-align: right;
        }
		.receipt-right h5 {
			font-size: 16px;
			font-weight: bold;
			margin: 0 0 7px 0;
		}
		.receipt-right p {

			font-size: 12px;
			margin: 0px;
		}
		.receipt-right p i {
			text-align: center;
			width: 18px;
		}
		.receipt-main td {
			padding: 9px 20px !important;
		}
		.receipt-main th {
			padding: 13px 20px !important;
		}
		.receipt-main td {
			font-size: 13px;
			font-weight: initial !important;
		}
		.receipt-main td p:last-child {
			margin: 0;
			padding: 0;
		}	
		.receipt-main td h2 {
			font-size: 20px;
			font-weight: 900;
			margin: 0;
			text-transform: uppercase;
		}
		.receipt-header-mid .receipt-left h1 {
			font-weight: 100;
			margin: 34px 0 0;
			text-align: right;
			text-transform: uppercase;
		}
		.receipt-header-mid {
			margin: 24px 0;
			overflow: hidden;
		}
		
		#container {
			background-color: #dcdcdc;
		}
        .table{
            caption-side: bottom;
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            vertical-align: top;
            border-color: #dee2e6;
        }
        .table>thead {
            vertical-align: bottom;
        }
        tbody, td, tfoot, th, thead, tr {
            border-color: inherit;
            border-style: solid;
            border-width: 0;
        }
        .table-bordered  tbody tr td{
            border-width: 1px 1px;
        }
  </style>
</head>
<body>

<div class="container">
<div class="col-md-12">   
 <div class="row">
            <div class="row">
    			<div class="receipt-header row">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="receipt-left">
							<img class="img-responsive" alt="iamgurdeeposahan" src="{{ asset('storage/images/settings').'/'.$settings['logo'] }}" style="width: 71px;">
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6" style="float:right;margin-top: -30px;">
						<div class="receipt-right text-right">
							<h5>Roda Taxi</h5>
							<p>{{$settings['head_office_number']}} <i class="fa fa-phone"></i></p>
							<p>{{$settings['help_email']}} <i class="fa fa-envelope-o"></i></p>
							<p>India <i class="fa fa-location-arrow"></i></p>
						</div>
                        <div class="receipt-left text-right">
							<h4>INVOICE <br>#{{$request_detail->request_number}}</h4>
						</div>
					</div>
				</div>
            </div>
			
			<div class="row">
				<div class="receipt-header receipt-header-mid row">
					<div class="col-xs-8 col-sm-8 col-md-8 text-left">
						<div class="receipt-right">
							<h5>{{$request_detail->userDetail ? $request_detail->userDetail->firstname." ".$request_detail->userDetail->lastname : ''}} </h5>
							<p><b>Mobile :</b> {{$request_detail->userDetail ? $request_detail->userDetail->phone_number : ''}}</p>
							<p><b>Email :</b> {{$request_detail->userDetail ? $request_detail->userDetail->email : ''}}</p>
							<p><b>Address :</b> {{$request_detail->userDetail ? $request_detail->userDetail->address : ''}}</p>
						</div>
					</div>
				</div>
            </div>
			
            <div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-md-9">Base Price ({{$request_detail->requestBill ? $request_detail->requestBill->base_distance : '0'}} KM)</td>
                            <td class="col-md-3"><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->base_price : '0.0'}}/-</td>
                        </tr>
                        <tr>
                            <td class="col-md-9">Distance Price ({{$request_detail->requestBill ? $request_detail->requestBill->price_per_distance : '0.0'}})</td>
                            <td class="col-md-3"><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->distance_price : '0.0'}}/-</td>
                        </tr>
                        <tr>
                            <td class="col-md-9">Waiting Price ({{$request_detail->requestBill ? $request_detail->requestBill->price_per_time : '0.0'}})</td>
                            <td class="col-md-3"><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->waiting_charge : '0.0'}}/-</td>
                        </tr>
                        <tr>
                            <td class="col-md-9">Booking Fee</td>
                            <td class="col-md-3"><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->booking_fees : '0.0'}}/-</td>
                        </tr>
                        <tr>
                            <td class="col-md-9">Hill Station Amount</td>
                            <td class="col-md-3"><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->hill_station_price : '0.0'}}/-</td>
                        </tr>
                        <tr>
                            <td class="col-md-9">Driver Beta</td>
                            <td class="col-md-3"><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->driver_commision : '0.0'}}/-</td>
                        </tr>
                        <tr>
                            <td class="col-md-9">Roda Commission</td>
                            <td class="col-md-3"><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->admin_commision : '0.0'}}/-</td>
                        </tr>
                        <tr>
                            <td class="text-right">
                            <p>
                                <strong>Total Amount: </strong>
                            </p>
                            <p>
                                <strong>Service Tax: </strong>
                            </p>
							<p>
                                <strong>Promo Amount: </strong>
                            </p>
							</td>
                            <td>
                            <p>
                                <strong><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->sub_total : '0.0'}}/-</strong>
                            </p>
                            <p>
                                <strong><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->service_tax : '0.0'}}/-</strong>
                            </p>
							<p>
                                <strong><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->promo_discount : '0.0'}}/-</strong>
                            </p>
							</td>
                        </tr>
                        <tr>
                           
                            <td class="text-right"><h2><strong>Total: </strong></h2></td>
                            <td class="text-left text-danger"><h2><strong><i class="fa fa-inr"></i> {{$request_detail->requestBill ? $request_detail->requestBill->requested_currency_symbol.' '.$request_detail->requestBill->total_amount : '0.0'}}/-</strong></h2></td>
                        </tr>
                    </tbody>
                </table>
            </div>
			
			<div class="row">
				<div class="receipt-header receipt-header-mid receipt-footer row">
					<div class="col-xs-8 col-sm-8 col-md-8 text-left">
						<div class="receipt-right">
							<p><b>Date :</b> {{ date('d-m-Y',strtotime($request_detail->trip_start_time))}}</p>
							<h5 style="color: rgb(140, 140, 140);">Thanks for traveling.!</h5>
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4">
						<div class="receipt-left">
							<h1>Stamp</h1>
						</div>
					</div>
				</div>
            </div>
	</div>
</div>
</div>

</body>
</html>
