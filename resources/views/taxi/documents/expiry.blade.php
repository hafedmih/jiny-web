@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('document_expiry_soon') }}</h5>
        </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('driver-name') }}</th>
                    <th>{{ __('document-name') }}</th>
                    <th>{{ __('expiry_days') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($document as $key => $documents)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td><a href="{{ route('driverEdit',$documents->slug) }}">{!! $documents->firstname !!} {!! $documents->lastname !!}</a><br>{!! $documents->phone_number !!}</td>
                        <td>{!! $documents->document_name !!} </td>
                        <td>{!! $documents->days !!} Days</td> 
                       
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>
<!-- /horizontal form modal -->

<script type="text/javascript">
    var i =1;
    function editAction(actionUrl){
        $(".delete_row").click();
        $("#add_row").hide();
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-document') }}");
                $('#errorbox').hide();
                // $('#saveBtn').html("Edit Complaint");
                $('#saveBtn').val("edit_document");
                $('#roleModel').modal('show');
                $('#document_slug').val(data.data.slug);
                $('#document_name').val(data.data.document_name);
                $('#group_by').val(data.data.group_by);
                if(data.data.requried == 1){
                    $('.required').prop('checked',true);
                }
                else{
                    $('.required').prop('checked',false);
                }
                if(data.data.identifier == 1){
                    $('.identifier').prop('checked',true);
                }
                else{
                    $('.identifier').prop('checked',false);
                }
                if(data.data.expiry_date == 1){
                    $('.experied_value').prop('checked',true);
                }
                else if(data.data.expiry_date == 2){
                    $('.issed_value').prop('checked',true);
                }
                else{
                    $('.experied_value').prop('checked',false);
                    $('.issed_value').prop('checked',false);
                }
                $('#required_value_0').val(data.data.requried);
                $('#identifier_value_0').val(data.data.identifier);
                $('#experi_value_0').val(data.data.expiry_date);
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    $("#add_row").on('click',function(){

        $("#tbody").append(text);
        i++;
    })

    $(document).on('click',".delete_row",function(){
        var row = $(this).parents('tr');
        $(row).remove();
    })

    $(document).on('click',".required",function(){
        var id = $(this).attr('id');
        if($(this).is(":checked")){
            $("#required_value_"+id).val('1');
        }
        else{
            $("#required_value_"+id).val('0');
        }
    })

    $(document).on('click',".identifier",function(){
        var id = $(this).attr('id');
        if($(this).is(":checked")){
            $("#identifier_value_"+id).val('1');
        }
        else{
            $("#identifier_value_"+id).val('0');
        }
    })
    $(document).on('click',".experied",function(){
        var id = $(this).attr('id');
        if($(this).is(":checked")){
            $("#experi_value_"+id).val($(this).val());
        }
        else{
            $("#experi_value_"+id).val($(this).val());
        }
    })
  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });


    $('#add_new_btn').click(function () {
        $('#complaint_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-document') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_document");
        // $('#saveBtn').html("Save Complaint");
        $('#errorbox').hide();
        $("#add_row").show();
        $(".delete_row").click();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_document'){
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('documentsUpdate') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-updated') }}",
                            text: "{{ __('data-updated-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                                // $("#reloadDiv").load("{{ route('documents') }}");
                                location.reload();
                            });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.error);
                    $('#errorContent').html('');
                    $.each(err.errors, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtn').html("{{ __('save-changes') }}");
                }
            });
        }else{
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('documentsSave') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-added') }}",
                            text: "{{ __('data-added-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                            
                                // $("#reloadDiv").load("{{ route('documents') }}");
                                location.reload();
                            });
                        
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#errorbox').show();
                        var err = eval("(" + xhr.responseText + ")");
                        // console.log(err.error);
                        $('#errorContent').html('');
                        $.each(err.errors, function(key, value) {
                            $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                        });
                        $('#saveBtn').html("{{ __('save-changes') }}");
                    }
                });
        }
    });



  });
</script>
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
