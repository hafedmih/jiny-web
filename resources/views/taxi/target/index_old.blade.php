@extends('layouts.app')

@section('content')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">{{ __('reward-management') }} </span> </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> {{ __('home') }}</a>
                <a href="" class="breadcrumb-item">{{ __('reward-management') }}</a>
                <!-- <span class="breadcrumb-item active">Basic inputs</span> -->
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>       
    </div>
</div>
<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('reward-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-reward'))
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
                    <th>{{ __('reward_name') }}</th>
                    <th>{{ __('reward_icon') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reward_list as $key => $reward)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $reward->reward_name !!}</td>
                       <td><img src="{{ asset($reward->reward_icon)}}" height="40px" width="40px" alt="" /></td>
                        <td>@if($reward->status == 1)
                                <span class="badge badge-primary">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td> 
                       
                        <td>                     
                            @if(auth()->user()->can('delete-reward'))
                                <a href="" class="btn bg-purple-400 btn-icon rounded-round legitRipple" onclick="Javascript: return deleteAction('$reward->slug', `{{ route('rewardDelete',$reward->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Delete"> <i class="icon-trash"></i> </a>
                            @endif
                            @if(auth()->user()->can('status-change-reward'))
                                <a  href="" class="btn bg-brown-400 btn-icon rounded-round legitRipple" onclick="Javascript: return activeAction(`{{ route('rewardActive',$reward->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="change status" ><i class="icon-user-check"></i></a>
                            @endif
                        </td>
                    </tr>
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
                            <div class="row">
                                <div class="col-md-6"> 
                                    <label class="col-form-label">{{ __('reward_name') }}</label>
                                    <input type="text" placeholder="Reward Name" id="reward_name" class="form-control" name="reward_name">
                                    <input type="hidden" name="reward_id" id="reward_id">
                                </div>
                                <div class="col-md-6 ml-auto">
                                    <label class="col-form-label">{{ __('reward_icon') }}</label>
                                    <input type="file" class="form-control" id="reward_icon" name="reward_icon" >
                                </div>
                            </div><br>

                            <div class="row">
                                <div class="col-md-6"> 
                                <label class="col-form-label">{{ __('reward_to') }}</label><br>
                                <label class="checkbox-inline"><input type="checkbox" name="reward_to[]" id="users_view" value="user">  User</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <label class="checkbox-inline"><input type="checkbox" name="reward_to[]" id="drivers_view" value="driver"> Driver</label></div>   
                            </div><br>

                            <div class="row">
                                <div class="col-md-6"> 
                                    <label class="col-form-label">{{ __('reward_user_count') }}</label>
                                    <input type="text" placeholder="Reward User Count" id="reward_user_count" class="form-control" name="reward_user_count">
                                </div>
                                <div class="col-md-6 ml-auto">
                                    <label class="col-form-label">{{ __('reward_driver_count') }}</label>
                                    <input type="text" placeholder="Reward Driver Count" id="reward_driver_count" class="form-control" name="reward_driver_count">
                                </div>
                            </div><br>

                            <div class="row">
                                <div class="col-md-3"> 
                                    <label class="col-form-label">{{ __('reward_user_from_date') }}</label>
                                    <input type="date" placeholder="{{ __('reward_user_from_date') }}" id="reward_user_from_date" class="form-control" name="reward_user_from_date">
                                </div>
                                <div class="col-md-3"> 
                                    <label class="col-form-label">{{ __('reward_user_to_date') }}</label>
                                    <input type="date" placeholder="{{ __('reward_user_to_date') }}" id="reward_user_to_date" class="form-control" name="reward_user_to_date">
                                </div>
                                <div class="col-md-3 ml-auto">
                                    <label class="col-form-label">{{ __('reward_Driver_from_date') }}</label>
                                    <input type="date" placeholder="{{ __('reward_Driver_from_date') }}" id="reward_driver_from_date" class="form-control" name="reward_driver_from_date">
                                </div>
                                <div class="col-md-3 ml-auto">
                                    <label class="col-form-label">{{ __('reward_Driver_to_date') }}</label>
                                    <input type="date" placeholder="{{ __('reward_Driver_to_date') }}" id="reward_driver_to_date" class="form-control" name="reward_driver_to_date">
                                </div>
                            </div><br>

                            <div class="row">
                                <div class="col-md-3"> 
                                    <label class="col-form-label">{{ __('reward_user_type') }}</label>
                                    <select id="reward_user_type" class="form-control" name="reward_user_type"  onclick="onclick_event();">
			                        <option value="">Select Reward User Type</option>
			                        <option value="1">{{ __('target_ride') }}</option>
			                        <option value="2">{{ __('target_amount') }}</option>
			                    </select>
                                </div>
                                <div class="col-md-3"  id="min_div" style="display: none;"> 
                                    <label class="col-form-label">{{ __('amount') }}</label>
                                    <input type="text" placeholder="{{ __('amount') }}" id="user_amount" class="form-control" name="amount">
                                </div>

                                <div class="col-md-3"  id="max_div" style="display: none;"> 
                                    <label class="col-form-label">{{ __('no_of_trips') }}</label>
                                    <input type="text" placeholder="{{ __('no_of_trips') }}" id="user_no_of_trips" class="form-control" name="no_of_trips">
                                </div>

                                <div class="col-md-3"> 
                                    <label class="col-form-label">{{ __('reward_driver_type') }}</label>
                                    <select id="reward_driver_type" class="form-control" name="reward_driver_type"  onclick="onclick_events();">
			                        <option value="">Select Reward User Type</option>
			                        <option value="1">{{ __('target_ride') }}</option>
			                        <option value="2">{{ __('target_amount') }}</option>
			                    </select>
                                </div>
                                <div class="col-md-3"  id="min_divs" style="display: none;"> 
                                    <label class="col-form-label">{{ __('amount') }}</label>
                                    <input type="text" placeholder="{{ __('amount') }}" id="driver_amount" class="form-control" name="amount">
                                </div>

                                <div class="col-md-3"  id="max_divs" style="display: none;"> 
                                    <label class="col-form-label">{{ __('no_of_trips') }}</label>
                                    <input type="text" placeholder="{{ __('no_of_trips') }}" id="driver_no_of_trips" class="form-control" name="no_of_trips">
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
        $('#reward_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-reward') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_reward");
        // $('#saveBtn').html("Save Complaint");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('reward_icon',$('#reward_icon').prop('files')[0]);
        formData.append('reward_name',$('#reward_name').val());
        // formData.append('reward_to',$('#reward_to').val());
        formData.append('reward_user_count',$('#reward_user_count').val());
        formData.append('reward_user_from_date',$('#reward_user_from_date').val());
        formData.append('reward_user_to_date',$('#reward_user_to_date').val());
        formData.append('reward_driver_count',$('#reward_driver_count').val());
        formData.append('reward_driver_from_date',$('#reward_driver_from_date').val());
        formData.append('reward_driver_to_date',$('#reward_driver_to_date').val());
        formData.append('reward_user_type',$('#reward_user_type').val());
        formData.append('reward_driver_type',$('#reward_driver_type').val());
        formData.append('amount',$('#user_amount').val());
        formData.append('no_of_trips',$('#user_no_of_trips').val());
        formData.append('reward_id',$('#reward_id').val());
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
            $.ajax({
                data: formData,
                url: "{{ route('rewardSave') }}",
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
                                $("#reloadDiv").load("{{ route('rewardlist') }}");
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


<script type="text/javascript">


    function onclick_users()
    {

     
    }
    
    function onclick_event()
    {
        var reward_user_type = $('#reward_user_type').val();
        if(reward_user_type == 1){
           // console.log('general so disappeared!');
            $('#min_div').hide();
            $('#max_div').show();
            $('#no_of_trips').prop('enabled','disabled');
            $('#amount').prop('enabled','disabled');
        }
        else if(reward_user_type == 2)
        {
            //console.log('offer upto so appeared');
            $('#min_div').show();
            $('#max_div').hide();
            $('#no_of_trips').prop('disabled', false);
            $('#amount').prop('enabled','disabled');
        }
    }


    function onclick_events()
    {
        var reward_driver_type = $('#reward_driver_type').val();
        if(reward_driver_type == 1){
           // console.log('general so disappeared!');
            $('#min_divs').hide();
            $('#max_divs').show();
            $('#no_of_trips').prop('enabled','disabled');
            $('#amount').prop('enabled','disabled');
        }
        else if(reward_driver_type == 2)
        {
            //console.log('offer upto so appeared');
            $('#min_divs').show();
            $('#max_divs').hide();
            $('#no_of_trips').prop('disabled', false);
            $('#amount').prop('enabled','disabled');
        }
    }

</script>
<script>
    $(document).ready(function() {

    $("#genderate_users").prop('disabled', true);
    $("#reward_user_count").prop('readonly', true);
    $("#reward_user_from_date").prop('readonly', true);
    $("#reward_user_to_date").prop('readonly', true);
    $("#reward_user_type").prop('readonly', true);

    $(document).on('click','#users_view',function() {
        if($(this).prop('checked')){
            $("#genderate_users").prop('disabled', false);
            $("#reward_user_count").prop('readonly', false);
            $("#reward_user_from_date").prop('readonly', false);
            $("#reward_user_to_date").prop('readonly', false);
            $("#reward_user_type").prop('readonly', false);
        }
        else{
            $("#genderate_users").prop('disabled', true);
            $("#reward_user_count").prop('readonly', true);
            $("#reward_user_from_date").prop('readonly', true);
            $("#reward_user_to_date").prop('readonly', true);
            $("#reward_user_type").prop('readonly', true);
            $("#user_list_tables").hide();
            $("#reward_user_count").val('');
            $("#reward_user_from").val('');
            $("#reward_user_to").val('');
            $("#reward_user_type").val('');
        }
    });
    $("#user_list_tables").hide();
    $(document).on('click','#genderate_users',function(){
        var count = $("#reward_user_count").val();
        var from = $("#reward_user_from_date").val();
        var to = $("#reward_user_to_date").val();
        var type = $("#reward_user_type").val();
        var text = "";
        //console.log(count);
        $.ajax({
          data: 'count='+count+'&from='+from+'&to='+to+'&type='+type,
          url: "{{ URL::Route('rewarduserget') }}",
          type:"POST",
          dataType: 'json',
          success: function (data) {
            console.log(data);
            $("#user_list_tables").show();
            if(data.data.length > 0){
                for (var i = 0; i < data.data.length; i++) {
                    if(data.data[i].firstname == null){
                        // text+="<tr><td>Null User Name<input type='hidden' name='user_id[]' value='"+data.data[i].id+"'></td></tr>";
                    }
                    else {
                        text+="<tr><td>"+(i+1)+") "+data.data[i].firstname+" "+data.data[i].lastname+"<input type='hidden' name='user_id[]' value='"+data.data[i].id+"'></td><td>"+data.data[i].rating+"</td><td>"+data.data[i].count+"</td></tr>";
                    }
                }
            }
            else{
                text+="<tr><td>No Users<input type='hidden' name='user_id[]' value=''></td></tr>";
            }
            $("#users_list").html(text);    
          }
        })
    });

    $("#genderate_driver").prop('disabled', true);
    $("#reward_driver_count").prop('readonly', true);
    $("#reward_driver_from").prop('readonly', true);
    $("#reward_driver_to_date").prop('readonly', true);
    $("#reward_driver_type_date").prop('readonly', true);

    $(document).on('click','#drivers_view',function() {
        if($(this).prop('checked')){
            $("#genderate_driver").prop('disabled', false);
            $("#reward_driver_count").prop('readonly', false);
            $("#reward_driver_from_date").prop('readonly', false);
            $("#reward_driver_to_date").prop('readonly', false);
            $("#reward_driver_type").prop('readonly', false);
        }
        else{
            $("#genderate_driver").prop('disabled', true);
            $("#reward_driver_count").prop('readonly', true);
            $("#reward_driver_from_date").prop('readonly', true);
            $("#reward_driver_to_date").prop('readonly', true);
            $("#reward_driver_type").prop('readonly', true);
            $("#driver_list_tables").hide();
            $("#reward_driver_count").val('');
            $("#reward_driver_from_date").val('');
            $("#reward_driver_to_date").val('');
            $("#reward_driver_type").val('');
        }
    })
    $("#driver_list_tables").hide();
    $(document).on('click','#genderate_driver',function(){
        var count = $("#reward_driver_count").val();
        var from = $("#reward_driver_from_date").val();
        var to = $("#reward_driver_to_date").val();
        var type = $("#reward_driver_type").val();
        var text1 = "";
       // console.log(count);
        $.ajax({
          data: 'count='+count+'&from='+from+'&to='+to+'&type='+type,
          url: "{{ URL::Route('rewarddriverget') }}",
          type:"POST",
          dataType: 'json',
          success: function (data) {
            console.log(data);
            $("#driver_list_tables").show();
            if(data.data.length > 0){
                for (var i = 0; i < data.data.length; i++) {
                    console.log(data.data[i]);
                    if(data.data[i].firstname == null){
                        // text1+="<tr><td>Null Driver Name<input type='hidden' name='user_id[]' value='"+data.data[i].id+"'></td></tr>";
                    }
                    else{
                        text1+="<tr><td>"+(i+1)+") "+data.data[i].firstname+" "+data.data[i].lastname+"<input type='hidden' name='driver_id[]' value='"+data.data[i].id+"'></td><td>"+data.data[i].rating+"</td><td>"+data.data[i].count+"</td></tr>";
                    }
                }
            }
            else{
                text1+="<tr><td>No Drivers<input type='hidden' name='driver_id[]' value=''></td></tr>";
            }
            $("#drivers_list").html(text1);
          }
        })
    });

</script>






@endsection
