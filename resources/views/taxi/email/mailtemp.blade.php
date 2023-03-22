<!DOCTYPE html>
<html lang="en">
<head>
  <title>Roda Taxi</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>  -->
    <style>
        body{
            background:#eee;
            margin-top:20px;
        }
        .text-danger strong {
            color: #9f181c;
        }
        .row {
            width: 100%;
            margin-top: calc(-1 * var(--bs-gutter-y));
            margin-right: calc(-.5 * var(--bs-gutter-x));
            margin-left: calc(-.5 * var(--bs-gutter-x));
        }
        .table-bordered>:not(caption)>*>* {
            border-width: 0 1px;
        }
        .col-md-6 {
            flex: 0 0 auto;
            width: 50%;
            float: left;
        }
        .col-md-8 {
            flex: 0 0 auto;
            width: 66.66666667%;
            float: left;
        }
        .col-md-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
            float: left;
        }
        h4 {
            font-size: 1.5rem;
        }
        .col-md-9 {
            flex: 0 0 auto;
            width: 75%;
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
        /* .table-bordered>:not(caption)>* {
            border-width: 1px 0;
        } */
  </style>
</head>
<body>

<div class="container">
<div class="col-md-12">   
 	<div class="row">
        <div class="receipt-main col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
            <div class="row">
    			<div class="receipt-header row">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="receipt-right ">
							<h5>Roda Taxi</h5>
							<!-- <p>{{$subject}} <i class="fa fa-phone"></i></p> -->
							<p>{!! $content !!} <i class="fa fa-envelope-o"></i></p>
							<p>India <i class="fa fa-location-arrow"></i></p>
						</div>
					</div>
				</div>
            </div>			
        </div>    
	</div>
</div>
</div>

</body>
</html>
