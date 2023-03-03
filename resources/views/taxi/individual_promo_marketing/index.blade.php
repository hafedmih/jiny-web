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
            <h5 class="card-title">{{ __('manage-promo-model') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-promo'))
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
                    <th>{{ __('promo_name') }}</th>
                    <th>{{ __('promo_percentage') }}</th>
                    <th>{{ __('promo_amount') }}</th>
                    <th>{{ __('trip_type') }}</th>
                    <th>{{ __('no_of_times_use') }}</th>
                    <th>{{__('status')}}</th>
                    <th>{{ __('action') }}</th>
                    
                </tr>
            </thead>
            <tbody>

                @foreach($promoList as $key => $value)
                    <tr>
                        <td>{{ ++$key }}</td>                        
                        <td>{!! $value->promo_name !!}</td>
                        <td>{!! $value->promo_percentage!!}</td>
                        <td>{!! $value->promo_amount !!}</td>
                        <td>{!! $value->trip_type !!}</td>
                        <td>{!! $value->no_of_times_use !!}</td>
                        <td>@if($value->status == 1)
                                <span class="badge badge-success">{{ __('open') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('close') }}</span>
                            @endif
                        </td> 
                        
                        <td>    
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-promo-marketing'))
                                <a href="#" onclick="Javascript: return editAction(`{{ route('promo-MarketingEdit',$value->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                @endif
                                @if(auth()->user()->can('delete-promo-marketing'))
                                <a href="#" onclick="Javascript: return deleteAction('$value->slug', `{{ route('promo-MarketingDelete',$value->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                @endif
                                @if(auth()->user()->can('active-promo-marketing'))
                                <a href="#" onclick="Javascript: return activeAction(`{{ route('promo-MarketingStatusChange',$value->slug) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
                                @endif
                            </div>          
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    
    <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" class="form-horizontal" enctype="multipart/form-data">
                              @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent"></span>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('promo_name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="promo_name" id="promo_name" class="form-control"  placeholder="{{ __('promo_name')}}" />
                                <input type="hidden" name="promo_id" id="promo_id" /> 
                            </div>
                        </div>

                        <div class="form-group row required">
                            <lable class="col-form-lable col-sm-3">{{ __('target_amount') }}</lable>
                            <div class="col-sm-9">
                                <input type="text" name="target_amount" id="target_amount" class="form-control" placeholder="{{ __('target_amount') }}">
                            </div>
                        </div>

                       <div class="form-group row required">
                           <label class="col-form-label col-sm-3">{{ __('promo_type') }}</label>
                           <div class="col-sm-9">
                               <select class="form-control" name="promo_type" id="promo_type">
                                   <option value="">Select Promo type</option>
                                   <option value="1">{{ __('fixed') }}</option>
                                   <option value="2">{{ __('percentage') }}</option>
                               </select>
                               
                           </div>
                       </div>

                        <div class="form-group row promo_percentage">
                            <label class="col-form-label col-sm-3">{{ __('promo_percentage') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="promo_percentage" id="promo_percentage" class="form-control"  placeholder="{{ __('promo_percentage')}}" />
                                
                            </div>
                        </div>
                        <div class="form-group row promo_amount">
                            <label class="col-form-label col-sm-3">{{ __('promo_amount') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="promo_amount" id="promo_amount" class="form-control"  placeholder="{{ __('promo_amount')}}" />
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('trip_type') }}</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="trip_type" id="trip_type">
			                        <option value="">Select Type</option>
                                    <option value="LOCAL">{{ __('local') }}</option>
                                    <option value="RENTAL">{{ __('rental') }}</option>
                                    <option value="OUTSTATION">{{ __('outstation') }}</option>
		                        </select>
                                
                            </div>
                        </div>

                        <div class="form-group row required">
                            <lable class="col-form-lable col-sm-3">{{ __('no_of_times_use') }}</lable>
                            <div class="col-sm-9">
                                <input type="text" name="no_of_times_use" id="no_of_times_use" class="form-control" placeholder="{{ __('no_of_times_use') }}">
                            </div>
                        </div>
                        
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
</div>
<!-- /horizontal form modal -->


<script type="text/javascript">
    $(".promo_percentage").hide();
    $(".promo_amount").hide();
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                 console.log(data);
                $('#modelHeading').html("{{ __('edit-promo') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit-promo");
                $('#saveBtn').show();
                $('#roleModel').modal('show');
                $('#promo_name').val(data.promo.promo_name);
                $('#promo_id').val(data.promo.slug);
                $('#promo_percentage').val(data.promo.promo_percentage);
                $('#promo_amount').val(data.promo.promo_amount);
                $('#trip_type').val(data.promo.trip_type);
                $('#promo_type').val(data.promo.promo_amount_type);
                if(data.promo.promo_amount_type == 1){
                    $(".promo_percentage").hide();
                    $(".promo_amount").show();
                    $("#promo_percentage").val('');
                }
                else{
                    $(".promo_percentage").show();
                    $(".promo_amount").hide(); 
                    $("#promo_amount").val('');
                }
                $('#target_amount').val(data.promo.target_amount);
                $('#no_of_times_use').val(data.promo.no_of_times_use);
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });


    $('#add_new_btn').click(function () {
        $('#promo_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-promo') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_promo");
        $('#errorbox').hide();
        $("#view_image").hide();
    });

    $("#promo_type").on('change',function(){
        var value = $(this).val();
        if(value == 1){
            $(".promo_percentage").hide();
            $(".promo_amount").show();
            $("#promo_percentage").val('');
            $("#promo_amount").val('');
        }
        else{
            $(".promo_percentage").show();
            $(".promo_amount").hide();
            $("#promo_percentage").val('');
            $("#promo_amount").val('');
        }
    })

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
       
        formData.append('promo_name',$('#promo_name').val());
        formData.append('promo_percentage',$('#promo_percentage').val());
        formData.append('promo_id',$('#promo_id').val());
        formData.append('promo_amount',$('#promo_amount').val());
        formData.append('trip_type',$('#trip_type').val());
        formData.append('no_of_times_use',$('#no_of_times_use').val());
        formData.append('target_amount',$('#target_amount').val());
        formData.append('promo_type',$('#promo_type').val());

        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
        if(btnVal == 'edit-promo'){
            $.ajax({
                data: formData,
                url: "{{ route('promo-MarketingUpdate') }}",
                type: "POST",
                // enctype: 'multipart/form-data',
                dataType: 'json',
                contentType : false,
                processData: false,
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-updated') }}",
                            text: "{{ __('data-updated-successfully') }}",
                            icon: "success",
                            }).then((value) => {
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
        }else{
            $.ajax({
                data: formData,
                url: "{{route('promo-MarketingSave')}}",
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
                            
                                location.reload();
                            });
                        
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#errorbox').show();
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err);
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
@endsection
