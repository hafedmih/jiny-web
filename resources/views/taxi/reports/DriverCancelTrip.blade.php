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
            <h5 class="card-title">{{ __('driver-cancel-request') }}</h5>
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
        <table id="itemList" class="table datatable-button-print-columns1">
            <thead>
                <tr>
                    <th>{{ __('s.no') }}</th>
                    <th>{{ __('request_number') }}</th>
                    <th>{{ __('Customer Name') }}</th>
                    <th>{{ __('driver_name')}}</th>
                    <th>{{ __('action')}}</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($list as $key => $list)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $list->request_number }}</td>
                        <td>{{ $list->userDetail ? $list->userDetail->firstname : '' }} {{ $list->userDetail ? $list->userDetail->lastname : '' }}</td>
                        <td>{{ $list->driverDetail ? $list->driverDetail->firstname : '' }} {{ $list->driverDetail ? $list->driverDetail->lastname : '' }}</td>
                        <td><a href="" class="btn btn-danger" ><i class="icon-trash"></i></a></td>
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
