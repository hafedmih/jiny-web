@extends('layouts.app')

@section('content')
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">
<style>
    .row.required .col-form-label:after{
                    content:" *";
                    color: red;
                    weight:100px;
    }
</style>


<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('target-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-target'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
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
                    <th>{{ __('driver_name') }}</th>
                    <th>{{ __('target_name') }}</th>
                    <th>{{ __('target_icon') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($target_list as $key => $target)
                @if( $target->user ? $target->user : '' )
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $target->user->firstname}} {{ $target->user->lastname}}</td>
                        <td>{!! $target->target_name !!}</td>
                       <td><img src="{{$target->target_icon}}" height="40px" width="40px" alt="" /></td>
                        <td>@if($target->status == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td> 
                       
                        <td>                     
                            @if(auth()->user()->can('delete-target'))
                                <a href="" class="btn bg-purple-400 btn-icon rounded-round legitRipple" onclick="Javascript: return deleteAction('$target->slug', `{{ route('targetDelete',$target->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete"> <i class="icon-trash"></i> </a>
                            @endif
                            @if(auth()->user()->can('status-change-target'))
                                <a  href="" class="btn bg-brown-400 btn-icon rounded-round legitRipple" onclick="Javascript: return activeAction(`{{ route('targetActive',$target->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="change status" ><i class="icon-user-check"></i></a>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade bd-example-modal-lg" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent"></span>
                        </div>

                        <div class="container">
                            <div class="row required">
                                 <div class="col-md-3 ml-auto ">
                                    <label class="col-form-label">{{ __('zone_name') }}</label>
                                    <select id="service_location" class="form-control" name="service_location">
                                        <option value="">Select Zone</option>
                                        @foreach($zone_list as $key => $zone)
                                        <option value="{{ $zone->id}}">{{ $zone->zone_name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 ml-auto"> 
                                    <label class="col-form-label">{{ __('target_name') }}</label>
                                    <input type="text" placeholder="{{ __('target_name') }}" id="target_name" class="form-control" name="target_name">
                                    <input type="hidden" name="target_id" id="target_id">
                                </div>
                                <div class="col-md-6 ml-auto">
                                    <label class="col-form-label">{{ __('target_icon') }}</label>
                                    <input type="file" class="form-control" id="target_icon" name="target_icon" >
                                </div>
                                
                            </div><br>


                            <div class="row">
                           
                                <div class="col-md-4 ">
                                    <label class="col-form-label">{{ __('target_select_package') }}</label>
                                    <select id="target_select_package" class="form-control" name="target_select_package"  onclick="onclick_package();">
			                        <option value="">Select Target Package</option>
			                        <option value="1">Select Package Duration</option>
			                        <option value="2">Select Date</option>
			                        </select>                               
                                </div>

                                <div class="col-md-4 ml-auto">
                                    <label class="col-form-label">{{ __('driver_list') }}</label>
                                    <select name="driver_id[]" class="form-control" id="driver_id">
                                    </select>
                              
                                </div>

                                <div class="col-md-4 ml-auto" id="duration">
                                    <label class="col-form-label">{{ __('duration') }}</label>
                                    <select id="target_duration" class="form-control" name="target_duration">
			                        <option value="">Select Target Duration</option>
			                        <option value="1">Daily</option>
			                        <option value="2">Weekly</option>
			                        <option value="3">Monthly</option>
			                        <option value="4">Yearly</option>
			                        </select>                               
                                </div>

                              
                            </div><br>

                            <div class="row">

                            <div class="col-md-6" id="from_date">
                                    <label class="col-form-label">{{ __('target_Driver_from_date') }}</label>
                                    <input type="date" placeholder="{{ __('target_Driver_from_date') }}" id="target_driver_from_date" class="form-control" name="target_driver_from_date">
                                </div>
                                <div class="col-md-6  ml-auto" id="to_date"> 
                                    <label class="col-form-label">{{ __('target_Driver_to_date') }}</label>
                                    <input type="date" placeholder="{{ __('target_Driver_to_date') }}" id="target_driver_to_date" class="form-control" name="target_driver_to_date">
                                </div>
                            </div><br>

                            <div class="row required">
                                

                                <div class="col-md-3"> 
                                    <label class="col-form-label">{{ __('target_driver_type') }}</label>
                                    <select id="target_driver_type" class="form-control" name="target_driver_type"  onclick="onclick_events();">
			                        <option value="">{{ __('target_driver_type') }}</option>
			                        <option value="1">{{ __('target_ride') }}</option>
			                        <option value="2">{{ __('target_amount') }}</option>
			                    </select>
                                </div>
                                <div class="col-md-3"  id="min_divs" style="display: none;"> 
                                    <label class="col-form-label">{{ __('amount') }}</label>
                                    <input type="number" placeholder="{{ __('amount') }}" id="amount" class="form-control" name="amount">
                                </div>

                              

                                <div class="col-md-3"  id="max_divs" style="display: none;"> 
                                    <label class="col-form-label">{{ __('no_of_trips') }}</label>
                                    <input type="text" placeholder="{{ __('no_of_trips') }}" id="no_of_trips" class="form-control" name="no_of_trips">
                                </div>

                                <div class="col-md-3"  id="achieve" style="display: none;"> 
                                    <label class="col-form-label">{{ __('achieve_amount') }}</label>
                                    <input type="text" placeholder="{{ __('achieve_amount') }}" id="achieve_amount" class="form-control" name="achieve_amount">
                                </div>

                              
                            </div>

                        </div>
                        <br><br>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                            <button type="submit" id="saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- /horizontal form modal -->

<script type="text/javascript">
  
  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });


    $('#add_new_btn').click(function () {
        $('#target_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-target') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_target");
        // $('#saveBtn').html("Save Complaint");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('target_icon',$('#target_icon').prop('files')[0]);
        formData.append('target_name',$('#target_name').val());
        formData.append('service_location',$('#service_location').val());
        formData.append('target_select_package',$('#target_select_package').val());
        formData.append('target_duration',$('#target_duration').val());
        formData.append('driver_id',$('#driver_id').val());
        formData.append('target_driver_from_date',$('#target_driver_from_date').val());
        formData.append('target_driver_to_date',$('#target_driver_to_date').val());
        formData.append('target_driver_type',$('#target_driver_type').val());
        formData.append('amount',$('#amount').val());
        formData.append('achieve_amount',$('#achieve_amount').val());
        formData.append('no_of_trips',$('#no_of_trips').val());
        formData.append('target_id',$('#target_id').val());
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
            $.ajax({
                data: formData,
                url: "{{ route('targetSave') }}",
                type: "POST",
                dataType: 'json',
                contentType : false,
                processData: false,
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-added') }}",
                            text: "{{ __('data-added-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                                // $("#reloadDiv").load("{{ route('targetlist') }}");
                                location.reload();
                            });
                        
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


<script src="{{ asset('backend/assets/js/jquery.multiselect.js') }}"></script>
<script type="text/javascript">

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
                data.forEach(element => {
                    text += '<option value='+element.user_id+'>'+element.users.firstname+' '+element.users.lastname+'</option>'
                }); 
                $("#driver_id").attr('multiple','multiple');
                $("#driver_id").html(text); 
                $('#driver_id').multiselect('reload');
                
            },

            error: function (xhr, ajaxOptions, thrownError) {
                    var err = eval("(" + xhr.responseText + ")");
                 
                    
                }
            
        })


    });


    

</script>


<script>
$('#driver_id').multiselect({
                    columns: 1,
                    placeholder: 'Select Drivers List',
                    search: true,
                    selectAll: true
                });
</script>

@endsection
