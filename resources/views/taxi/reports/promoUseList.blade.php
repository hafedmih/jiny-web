@extends('layouts.app')

@section('content')

<div class="content">

   
 
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">{{ __('promo_use_reports') }}</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <table class="table datatable-button-print-columns1" id="roletable">
                            <thead>
                                <tr>
                                    <th>{{ __('sl') }}</th>
                                    <th>{{ __('date') }}</th>
                                    <th>{{ __('use_to_promo') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $key => $value)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{$value->date}}</td>
                                        <td>{{$value->total}}</td>
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


@endsection
