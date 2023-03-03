@extends('layouts.app')

@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<div class="content">
     
				
    <div class="card">
        
        <div class="card-header header-elements-inline">
             <h5 class="card-title">{{ __('income_reports') }}</h5>
             
         </div>
         <br>
        <div class="card-body">
            <div class="row">
                             
                <div class="col-lg-6">
                    
                    <div class="card">
                        <div class="card-header header-elements-inline" id="columns_stacked" >
                            <h5 class="card-title">{{ __('total_income') }}</h5>
                            <div class="header-elements">
                                
                                <form class="date"action="">
                                From Date: <input type="date" name="from" >
                                To Date: <input type="date" name="to" ><br>
                                </form>

                                <button>Submit</button>
                            </div>
                        </div>
                        <br><br>
                        <div class="card-body text-center">
                        <a style="color:#222" href="{{ route('incomeList','TOTAL') }}">
                            <i class="icon-cash icon-2x text-success-200 border-success-400 border-3 rounded-round p-3 mb-3 mt-1"></i>
                            <h5 class="card-title">{{ __('total') }}</h5>
                            
                            <h5 class="font-weight-semibold mb-1 total_amount_add">{{$amount['currency']}} {{$amount['total_amount_add']}}</h5>
                            <br><br>                                                                                                          
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                    <div class="card-body text-center">
                        <a style="color:#222" href="{{ route('incomeList','ADMIN') }}">
                            <i class="icon-user-tie icon-2x text-success-200 border-success-400 border-3 rounded-round p-3 mb-3 mt-1"></i>
                            <h5 class="card-title">{{ __('admin_commission') }}</h5>
                            <h5 class="font-weight-semibold mb-1 admin_amount_add">{{$amount['currency']}} {{$amount['admin_amount_add']}}</h5>
                        </a>
                        </div>
                    </div>

                    <div class="card">
                    <div class="card-body text-center">
                       <a style="color:#222" href="{{ route('incomeList','DRIVER') }}">
                            <i class="icon-car icon-2x text-info-200 border-info-400 border-3 rounded-round p-3 mb-3 mt-1"></i>
                            <h5 class="card-title">{{ __('driver_commission') }}</h5>
                            <h5 class="font-weight-semibold mb-1 driver_amount_add">{{$amount['currency']}} {{$amount['driver_amount_add']}}</h5>
</a>
                        </div>
                    </div>
                    <div class="card">
                    <div class="card-body text-center">
                    <a style="color:#222" href="{{ route('incomeList','TAX') }}">
                            <i class="icon-cogs icon-2x text-danger-200 border-danger-400 border-3 rounded-round p-3 mb-3 mt-1"></i>
                            <h5 class="card-title">{{ __('service_tax') }}</h5>
                            <h5 class="font-weight-semibold mb-1 tax_amount_add">{{$amount['currency']}} {{$amount['tax_amount_add']}}</h5>
</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    

    <script>

        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        $("button").click(function(){
            var values = $(".date").serialize();
        // });
        // });

        // $(document).on('change','#from',function(){
        // var values = $(this).val();
        console.log(values)
        $.ajax({
            url: "{{ url('income-reports') }}",
            type: "POST",
            dataType: 'json',
            data:values,
            success: function (values) {
                data = values;
                // console.log(data);
                // columns_stacked_element_fun(columns_stacked_element,data.week_days,data.total_amount,data.admin_amount,data.driver_amount,data.tax_amount,'','',"Total,Admin,Driver,service Tax");
                $(".admin_amount_add").text(data.amount.currency+' '+data.amount.admin_amount_add);
                $(".driver_amount_add").text(data.amount.currency+' '+data.amount.driver_amount_add);
                $(".total_amount_add").text(data.amount.currency+' '+data.amount.total_amount_add);
                $(".tax_amount_add").text(data.amount.currency+' '+data.amount.tax_amount_add);


            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.errors);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    });
});
    
    </script>
@endsection