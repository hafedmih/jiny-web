@extends('layouts.app')

@section('content')
<style>
    .form-group.required .col-form-label:after {
                content:" *";
                color: red;
                weight:100px;
            }

</style>

<div class="content">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('card_payment') }}</h5>
        </div>
    </div>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('reports')}}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="form" action="#" >
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From: </label>
                        <input type="date" class="form-control" name="from" placeholder="Enter starting date" value="{{$request->from}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To: </label>
                        <input type="date" class="form-control " name="to" placeholder="Enter end date" value="{{$request->to}}">
                    </div>
                </div>
                
                <div class="col-md-3" style="padding-top:25px;">
                    <div class="form-group">
                    <button type="submit" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-filter4 mr-2"></i> {{ __('filter') }}</button>

                    </div>
                </div>
              
            </div>	
            </form>
        </div>
        <table id="itemList" class="table datatable-button-print-columns1">
            <thead>
                <tr>
                    <th>{{ __('s.no') }}</th>
                    <th>{{ __('Customer Name') }}</th>
                    <th>{{ __('request_id')}}</th>
                    <th>{{ __('payment_id')}}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Date') }}</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($payment as $key => $list)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $list->userDetail ? $list->userDetail->firstname : '' }} {{ $list->userDetail ? $list->userDetail->lastname : '' }}</td>
                        <td>
                            <div>
                            <a style="color:#000" href="{{ route('requestView',$list->request_id) }}">{!! $list->requestDetail ? $list->requestDetail->request_number : '' !!}</a> 
                            </div>
                        </td>
                        <td>{{ $list->payment_id}}</td>
                        <td>{{ $list->amount}}</td>
                        <td>{{ $list->created_at}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- /horizontal form modal -->


<script type="text/javascript">
     var message = "{{session()->get('message')}}";
    var status = "{{session()->get('status')}}";

    if(message && status == true){
        swal({
            title: message,
            text: "{{ __('successfully') }}",
            icon: "success",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

    if(message && status == false){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }
  
</script>

@endsection
