@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-user-trips') }} </h5>
            <div class="header-elements">
                <div class="list-icons">
                {{ __('name') }} : {{$user->firstname}} {{$user->lastname}}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-body bg-success-400 has-bg-image">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0">{{$user->UserRequestDetail ? count($user->UserRequestDetail) : '0'}}</h3>
                        <span class="text-uppercase font-size-xs">{{ __('total_trips') }}</span>
                    </div>
                    <div class="ml-3 align-self-center">
                        <i class="icon-car icon-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-sm-6 col-xl-3">
            <div class="card card-body bg-blue-400 has-bg-image">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0">{{$user->driver ? $user->driver->total_accept : '0'}}</h3>
                        <span class="text-uppercase font-size-xs">{{ __('total_complete_trips') }}</span>
                    </div>
                    <div class="ml-3 align-self-center">
                        <i class="icon-car2 icon-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-body bg-danger-400 has-bg-image">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0">{{$user->driver ? $user->driver->total_reject : '0'}}</h3>
                        <span class="text-uppercase font-size-xs">{{ __('total_cancel_trips') }}</span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <i class="icon-close2 icon-3x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- <div class="col-sm-6 col-xl-3">
            <div class="card card-body bg-indigo-400 has-bg-image">
                <div class="media">
                    <div class="mr-3 align-self-center">
                        <i class="icon-enter6 icon-3x opacity-75"></i>
                    </div>

                    <div class="media-body text-right">
                        <h3 class="mb-0">245,382</h3>
                        <span class="text-uppercase font-size-xs">total visits</span>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
    <div class="card" id="tableDiv">
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('request_id') }}</th>
                    <th>{{ __('date') }}</th>
                    <th>{{ __('driver') }}</th>
                    <th>{{ __('pickup_address') }}</th>
                    <th>{{ __('drop_address') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('txt_Payment') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user->UserRequestDetail as $key => $docum)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $docum->request_number !!}</td>
                        <td>{{date("d/m/Y  h:i:s a",strtotime($docum->created_at))}}</td>
                        <td>{!! $docum->driverDetail ? $docum->driverDetail->firstname.' '.$docum->driverDetail->lastname : '' !!}</td>
                        <td>{!! $docum->requestPlace->pick_address !!}</td>
                        <td>{!! $docum->requestPlace->drop_address !!}</td>
                        <td>@if($docum->is_cancelled == 1)
                                <span class="badge badge-danger">{{ __('trip_cancelled') }}</span>
                            @elseif($docum->is_completed == 1)
                                <span class="badge badge-success">{{ __('trip_completed') }}</span>
                            @elseif($docum->is_driver_arrived == 1)
                                <span class="badge badge-warning">{{ __('trip_arrived') }}</span>
                            @elseif($docum->is_driver_started == 1)
                                <span class="badge badge-info">{{ __('trip_accepted') }}</span>
                            @elseif($docum->is_trip_start == 1)
                                <span class="badge badge-primary">{{ __('trip_started') }}</span>
                            @endif
                        </td> 
                        <td>@if($docum->is_paid == 1)
                                <span class="badge badge-primary">{{ __('paid') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('unpaid') }}</span>
                            @endif
                        </td> 
                        <td>                     
                            @if(auth()->user()->can('edit-driver-document'))
                                <a href="{{ route('requestView',$docum->id) }}" class="btn bg-success-400 btn-icon rounded-round legitRipple" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Trip View"> <i class="icon-eye"></i> </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
<!-- /horizontal form modal -->

<script type="text/javascript">
    var i =1;
    var approved = [];
    var denaited = [];
    $(".dated").hide();
    $(".hide").hide();

    function updateAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $('#roleForm').trigger("reset");
               console.log(data);
                $('#modelHeading').html("{{ __('edit-document') }}");
                $('#errorbox').hide();
                // $('#saveBtn').html("Edit Complaint");
                $('#saveBtn').val("edit_document");
                $('#roleModel').modal('show');
                $('#document_id').val(data.data.slug);
                $('#title').val(data.data.document_name);
                $('#date_required').val(data.data.expiry_date);
                $('#image').attr("src",data.data.document_image);
                if(data.data.expiry_date == 1){
                    $(".dated").show();
                    $('.date_lable').html("{{ __('experie-date') }}");
                    $('#expiry_date').val(data.data.expiry_dated);
                }
                else if(data.data.expiry_date == 2){
                    $(".dated").show();
                    $('.date_lable').html("{{ __('issue-date') }}");
                    $('#expiry_date').val(data.data.issue_dated);
                }
                else{
                    $(".dated").hide();
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    $('#document_image').change(function(){
          let reader = new FileReader();
          reader.onload = (e) => { 
            $("#image").attr('src',e.target.result);
          }
          reader.readAsDataURL(this.files[0]); 
    });

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $(".approved").click(function(){
        var id = $(this).attr('for');
        if(approved.indexOf($("#"+id).val()) == -1){
            approved = [];
            $(this).removeClass('btn-outline');
            $("#"+id).prop('checked', true);
            $('input[name="approved[]"]:checkbox:checked').each(function(i){
              approved[i] = $(this).val();
            });
            console.log(approved);
        }
        else{
            approved = [];
            $(this).addClass('btn-outline');
            $("#"+id).prop('checked', false);
            $('input[name="approved[]"]:checkbox:checked').each(function(i){
              approved[i] = $(this).val();
            });
            console.log(approved);
        }
    });

    $(".denaited").click(function(){
        var id = $(this).attr('for');
        if(denaited.indexOf($("#"+id).val()) == -1){
            denaited = [];
            $(this).removeClass('btn-outline');
            $("#"+id).prop('checked', true);
            $('input[name="denaited[]"]:checkbox:checked').each(function(i){
              denaited[i] = $(this).val();
            });
            console.log(denaited);
        }
        else{
            denaited = [];
            $(this).addClass('btn-outline');
            $("#"+id).prop('checked', false);
            $('input[name="denaited[]"]:checkbox:checked').each(function(i){
              denaited[i] = $(this).val();
            });
            console.log(denaited);
        }
    });

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('document_image',$('#document_image').prop('files').length > 0 ? $('#document_image').prop('files')[0] : '');
        formData.append('document_id',$('#document_id').val());
        formData.append('driver_id',$('#driver_id').val());
        formData.append('date_required',$('#date_required').val());
        formData.append('expiry_date',$('#expiry_date').val());
        $(this).html("{{ __('sending') }}");
        $("#errorbox").hide();
        $.ajax({
            data: formData,
            url: "{{ route('driverDocumentUpdate') }}",
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
                    window.location.href = "../driver-document/"+$('#driver_id').val();
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



    $('#upproved').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('approved_document_id',approved);
        formData.append('denaited_document_id',denaited);
        formData.append('driver_id',$('#driver_id').val());
        $.ajax({
            data: formData,
            url: "{{ route('driverDocumentApproved') }}",
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
                    window.location.href = "{{ route('driver') }}";
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
