@extends('layouts.app')

@section('content')


<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-sos') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-sos'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Sos list</h6>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-top nav-tabs-bottom nav-justified">
                    @foreach($languages as  $lang)
                    <li class="nav-item {{ $lang->id == 1  ? 'active ' : '' }}" ><a href="#{{$lang->code}}" class="nav-link " data-toggle="tab">{{ $lang->name }}</a></li>
                    @endforeach
            </ul> 
            
            
            <div class="tab-content">
                    @foreach($languages as $lang)
                        <div class="tab-pane {{ $lang->id == 1 ? 'active' : '' }}" id="{{$lang->code}}"> 
                            <table class="table datatable-button-print-columns1 absolute" id="roletable">
                                <thead>
                                <tr>
                                    <th>{{ __('sl') }}</th>
                                    <th>{{ __('Phone Number') }}</th>
                                    <th>{{ __('title') }}</th>
                                    <th>{{ __('status') }}</th>
                                    <th>{{ __('action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if(is_array($sosList) || is_object($sosList))
                                    @php($key = 0)
                                    @foreach($sosList["$lang->code"] as  $value)
                                            <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $value['phone_number'] }}</td>
                                            <td>{{ $value['title']}}</td>
                                            <td>
                                                @if($value['status'] == 1)
                                                    <span class="badge badge-success">{{ __('active') }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ __('inactive') }}</span>
                                                @endif
                                            </td>  
                                            <td>    
                                                <div >
                                                    <a class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7" ></i></a>
                                                    <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: relative; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                                        @if(auth()->user()->can('edit-sos')) 
                                                        <a href="#" onclick="Javascript: return editAction(`{{ route('sos-managementEdit', $value['slug']) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                                        @endif
                                                        @if(auth()->user()->can('delete-sos'))
                                                        <a href="#" onclick="Javascript: return deleteAction('$value->slug', `{{ route('sos-managementDelete', $value['slug']) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                        @endif
                                                        @if(auth()->user()->can('status-change-sos'))
                                                        <a href="#" onclick="Javascript: return activeAction( `{{ route('sos-managementChangeStatus', $value['slug']) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
                                                        @endif    
                                                    </div>
                                                </div>         
                                            </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>

   

    <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
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
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('phone-number') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="phone_number" id="phone_number" class="form-control"  placeholder="Phone Number" >
                                <input type="hidden" name="sos_id" id="sos_id">
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('Description') }}</label>
                            <div class="col-sm-9">
                                <textarea placeholder="Description" id="description" class="form-control" name="description"></textarea>
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('title') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Title" id="title" class="form-control" name="title">
                            </div>
                        </div>

                        <div class="form-group row hidding required">
                            <label class="col-form-label col-sm-3">{{ __('language') }}</label>
                            <div class="col-sm-9">
                                <select id="language" class="form-control" name="language">
                                <option value="">{{ __('Select language') }}</option>
                                @if($languages->count() > 0)
                                    @foreach($languages as $language)
                                    @if($language->status == 1)
                                    <option value="{{ $language->code }}">{{ $language->name }}</option>
                                    @endif
                                    @endforeach
                                @endif
			                    </select>
                            </div>
                        </div>


                        <div hidden class="form-group row required ">
                            <div class="col-sm-9">
                                <input type="text" value="1" id="status" class="form-control" name="status">
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
<!-- /horizontal form modal -->

<script type="text/javascript">
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-sos-management') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit-sos-management");
                $('#saveBtn').show();
                $('#roleModel').modal('show');
                $('#sos_id').val(data.key);
                $('#phone_number').val(data.sos.phone_number);
                $('#description').val(data.sos.description);
                $('#title').val(data.sos.title);
                $('#language').val(data.sos.language);
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

    function viewAction(actionUrl){
        
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $('#modelHeading').html("{{ __('view-sos-management') }}");
                $('#errorbox').hide();
                $('#saveBtn').hide();
                $('#roleModel').modal('show');
                $('#sos_id').val(data.key);
                $('#phone_number').val(data.sos.phone_number);
                $('#description').val(data.sos.description);
                $('#title').val(data.sos.title);
                $('#phone_number').attr('readonly', true);
                $('#description').attr('readonly', true);
                $('#title').attr('readonly', true);
               
            },

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
        $('#sos_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-Sos-Management') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_sos-management");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit-sos-management'){
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('sos-managementUpdate') }}",
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
                                // $("#reloadDiv").load("{{ route('sos-management') }}");
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
                data: $('#roleForm').serialize(),
                url: "{{ route('sos-managementSave') }}",
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
                            
                                // $("#reloadDiv").load("{{ route('sos-management') }}");
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
