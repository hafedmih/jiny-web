@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('driver-logs-list') }} </h5>
            <div class="header-elements">
                <div class="list-icons">
                    <h4>{{ __('name') }} : {{$user->firstname}} {{$user->lastname}}</h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card" id="tableDiv">
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('date') }}</th>
                    <th>{{ __('online') }}</th>
                    <th>{{ __('offline') }}</th>
                    <th>{{ __('working_hours') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user->DriverLogsList()->orderBy('id','DESC')->get() as $key => $logs)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! date("d-m-Y",strtotime($logs->date)) !!}</td>
                        <td>{!! $logs->online_time ? date("h:i:s A",strtotime($logs->online_time)) : '' !!}</td>
                        <td>{!! $logs->offline_time ? date("h:i:s A",strtotime($logs->offline_time)) : '' !!}</td>
                        <td>{!! $logs->working_time ? date("H",strtotime($logs->working_time)).' hours '.date("i",strtotime($logs->working_time)).' mins' : '' !!}</td>
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
