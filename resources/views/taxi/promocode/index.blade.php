@extends('layouts.app')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">


<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('promo-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-promocode'))
                    <a href="{{ route('promocreate')}}"> <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i>{{ __('add-new')}}</button></a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('select_offer_option') }}</th>
                    <th>{{__('target_amount')}}</th>
                    <th>{{ __('promo_code') }}</th>
                    <th>{{ __('promo_icon') }}</th>
                    <th>{{__('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($promolist as $key => $promo)
                    <tr>
                        <td>{{ ++$key }}</td>
                        
                        <td>
                            @if($promo->select_offer_option == 1) 
                                NewUser Promo Offer
                            @elseif ($promo->select_offer_option == 2) 
                                Distance Promo Offer
                            @elseif ($promo->select_offer_option == 3) 
                                Amount Promo Offer
                            @elseif ($promo->select_offer_option == 4) 
                                New Launch Promo Offer
                            @elseif ($promo->select_offer_option == 5) 
                                Individual Promo Offer
                            @elseif ($promo->select_offer_option == 0)
                               
                            @endif
                        </td>
                        <td>{{$currency}} {{$promo->target_amount}} </td>
                        <td>{{ $promo->promo_code}}</td>
                        <td><img src="{{ $promo->promo_icon}}" height="40px" width="auto" alt="image" /></td>
                        <td>@if($promo->status == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td> 
                        <td>    
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-promocode'))
                                <a href="{{ route('promoEdit',$promo->slug) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                @endif
                                @if(auth()->user()->can('delete-promocode'))
                                <a href="#" onclick="Javascript: return deleteAction('$promo->slug', `{{ route('promoDelete',$promo->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                @endif
                                @if(auth()->user()->can('status-change-promocode'))
                                <a href="#" onclick="Javascript: return activeAction(`{{ route('promoActive',$promo->slug) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
                                @endif
                            </div>          
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- /horizontal form modal -->


<script type="text/javascript">
    @if (session('success'))
        swal({
            title: "{{ __('data-updated') }}",
            text: "{{ session('success') }}",
            icon: "success",
        }).then((value) => {                            
            // $("#reloadDiv").load("{{ route('notification') }}");
        });
    @endif
    function onclick_events()
    {
        var target_driver_type = $('#target_driver_type').val();
        if(target_driver_type == 1){
           // console.log('general so disappeared!');
            $('#min_divs').hide();
            $('#max_divs').show();
            $('#achieve').show();
            $('#no_of_trips').prop('enabled','disabled');
            $('#amount').prop('enabled','disabled');
            $('#achieve_amount').prop('enabled','disabled');

        }
        else if(target_driver_type == 2)
        {
            //console.log('offer upto so appeared');
            $('#min_divs').show();
            $('#max_divs').hide();
            $('#achieves').show();
            $('#no_of_trips').prop('disabled', false);
            $('#amount').prop('enabled','disabled');
            $('#achieve_amount').prop('enabled','disabled')
        }
    }


    function onclick_package()
    {
        var target_select_package = $('#target_select_package').val();
        if(target_select_package == 1){
           // console.log('general so disappeared!');
            $('#from_date').hide();
            $('#to_date').hide();
            $('#duration').show();

        }
        else if(target_select_package == 2)
        {
            //console.log('offer upto so appeared');
            $('#from_date').show();
            $('#to_date').show();
            $('#duration').hide();
        }
    }

    $('#service_location').on('change', function()
    {
    var ServiceLocation = $("#service_location").val();
    var text = "";
    

    $.ajax({
          data: { "ServiceLocation": ServiceLocation },
          url: "{{ URL::Route('TargetDrivers') }}",
          type:"POST",
          dataType: 'json',
          success: function (data) {
              console.log(data);
              data.forEach(element => {
                text += '<option value='+element.user_id+'>'+element.users.firstname+'</option>'
                }); 
              
            $("#driver_id").html(text); 
            },

            error: function (xhr, ajaxOptions, thrownError) {
                    var err = eval("(" + xhr.responseText + ")");
                 
                    
                }
            
        })


    });


    

</script>


<script src="{{ asset('backend/assets/js/jquery.multiselect.js') }}"></script>
<script>
$('#driver_id').multiselect({
    columns: 1,
    placeholder: 'Select Drivers List'
});
</script>

@endsection
