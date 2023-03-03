@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('driver-complaints-list') }} </h5>
            <div class="header-elements">
                
            </div>
        </div>
    </div>
    
    <div class="card" id="tableDiv">
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('complaint') }}</th>
                    <th>{{ __('answer') }}</th>
                    <th>{{ __('category') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('Fine') }}</th>

                </tr>
            </thead>
            <tbody>
                @foreach($complaints_list as $key => $docum)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $docum->complaintDetails ? $docum->complaintDetails->title : '' !!}</td>
                        <td>{!! $docum->answer !!}</td>
                        <td>@if($docum->category == 1)
                                <span class="badge badge-primary">{{ __('complaints') }}</span>
                            @else
                                <span class="badge badge-info">{{ __('suggestion') }}</span>
                            @endif
                        </td> 
                        
                        <td>@if($docum->status == 1)
                                <span class="badge badge-primary">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td> 
                       
                         <td>                     
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal_form_inline" style="text-tra">Fine <i class="icon-plus2"></i></button>	
                         </td>  
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
	 <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('add-amount') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent"></span>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('phone-number') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="amount" id="amounts" class="form-control"  placeholder="Amount">
                                <input type="hidden" name="user" id="sos_id">
                            </div>
                        </div>
                        

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
  <!-- Inline form modal -->
				<div id="modal_form_inline" class="modal fade" tabindex="-1">
					<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Add Fine Amount</h5>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>

							<form action="#" class="modal-body form-inline justify-content-center">
								<label>Fine Amount:</label>
								<input type="text" style="padding-left:10px;" placeholder="Amount" name="fine_amount" id="fine_amount" class="form-control">

							      <label>Description:</label>
                                <textarea  style="padding-left:10px;" id="description" class="form-control" name="description"></textarea>
                           
                              
							    <br>
                            
                  <button type="button" class="btn bg-primary ml-sm-2 mb-sm-0" id="add_amount">Fine Amount <i class="icon-plus22"></i></button>
							</form>
						</div>
					</div>
				</div>
				<!-- /inline form modal -->


<!-- /horizontal form modal -->


<script>

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $(document).on('click','#add_amount',function(){
        var fine_amount = $("#fine_amount").val();
        var user_id = $("#user_id").val();
        var description = $("#description").val();

        
        var formData = new FormData();
        formData.append('fine_amount',fine_amount);
        formData.append('user_id',user_id);
        formData.append('description',description);


        $.ajax({
            data: formData,
            url: "{{ route('fineSave',$users->slug) }}",
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
                    window.location.href = "{{url('driver')}}";
                  

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
