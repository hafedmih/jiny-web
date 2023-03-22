@extends('layouts.app')

@section('content')


  

<div class="content" >
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('version-control-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('add-new-version'))
                    <button type="button" id="add_new_version" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i>{{ __('add-new') }}</button>
                    @endif
                    <!-- <a  class="btn bg-pink btn-sm legitRipple"><i class="icon-list2 mr-2"></i> List</a> -->
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="tableDiv">
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('version-number') }}</th>
                    <th>{{ __('version-code') }}</th>
                    <th>{{ __('application-type') }}</th>
                    <th>{{ __('description') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($versionList as $key => $version)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $version->version_number}}</td>
                        <td>{{ $version->version_code }}</td>
                        <td>{{ $version->application_type }}</td>
                        <td>{{ $version->description }}</td> 
                        <td>@if($version->status == "OPEN")
                                <span class="badge badge-success">{{ __('OPEN') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('CLOSE') }}</span>
                            @endif
                        </td> 
                        <td>    
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-version'))
                                <a href="#" onclick="Javascript: return editAction('$version->slug', `{{ route('versions.edit',$version->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                @endif                                
                                @if(auth()->user()->can('banned-version'))
                                <a href=""  onclick="Javascript: return banAction('$version->slug', `{{ route('versions.banned',$version->slug) }}`)" class="dropdown-item"> <i class="icon-user-cancel"></i>Banned</a>
                                @endif
                            </div>          
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<!-- Horizontal form modal -->
    <div id="versionModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="versionForm" name="versionForm" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <button type="button" class="close" data-dismiss="alert"><span>×</span></button>
                            <span id="errorContent"></span>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('version-number') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Enter Version Number" id="version_number" class="form-control" name="version_number">
                                <input type="hidden" name="version_id" id="version_id">

                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('version-code') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="Enter Version Code" id="version_code" class="form-control" name="version_code" readonly>
                            
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('application-type') }}</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="application_type" id="application_type">
                                    <option value="">Select Application Type</option>
                                    <option value="android">{{ __('android') }}</option>
                                    <option value="ios">{{ __('ios') }}</option>
                                </select>                          
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('description') }}</label>
                            <div class="col-sm-9">
                                <textarea rows="3" cols="3" name="description" id="description" class="form-control" placeholder="Enter Description"></textarea>
                            </div>
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtnVer" class="btn bg-primary">{{ __('add-new') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- /horizontal form modal -->

</div>

<script type="text/javascript">
    function editAction(slug, actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-project-version') }}");
                $('#errorbox').hide();
                $('#saveBtnVer').html("{{ __('edit-project-version') }}");
                $('#saveBtnVer').val("edit_version");
                $('#versionModel').modal('show');
                $('#version_id').val(data.data.slug);
                $('#version_number').val(data.data.version_number);
                $('#version_code').val(data.data.version_code);
                $('#application_type').val(data.data.application_type);
                $('#description').val(data.data.description);
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

    $('#add_new_version').click(function () {
        $.ajax({
                url: "{{ url('versions') }}/create",
                type: "GET",
                success: function (data) {
                    //console.log(data);
                    $('#version_id').val('');
                    $('#versionForm').trigger("reset");
                    $('#modelHeading').html("{{ __('new-project-version') }}");
                    $('#versionModel').modal('show');
                    $('#saveBtnVer').val("add_version");
                    $('#version_code').val(data);
                    $('#saveBtnVer').html("{{ __('save-version') }}");
                    $('#errorbox').hide();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.error);
                    $('#errorContent').html('');
                    $.each(err.error, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtnVer').html("{{ __('save-changes') }}");
                }
            });
    });
    $('#saveBtnVer').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');
        var btnVal = $(this).val();
        var slug = $('#version_id').val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_version'){
            $.ajax({
                data: $('#versionForm').serialize(),
                url: "{{ url('versions') }}/"+slug+"/edit",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                        $('#versionForm').trigger("reset");
                        $('#versionModel').modal('hide');
                        swal({
                            title: "{{ __('data-updated') }}",
                            text: "{{ __('data-updated-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                                $("#reloadDiv").load("{{ route('versions.index') }}");
                            });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.error);
                    $('#errorContent').html('');
                    $.each(err.error, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtnVer').html("{{ __('save-changes') }}");
                }
            });
        }else{
            $.ajax({
                data: $('#versionForm').serialize(),
                url: "{{ url('versions') }}/store",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                        $('#versionForm').trigger("reset");
                        $('#versionModel').modal('hide');
                        swal({
                            title: "{{ __('data-added') }}",
                            text: "{{ __('data-added-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                            
                                $("#reloadDiv").load("{{ route('versions.index') }}");
                            });
                        
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#errorbox').show();
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.error);
                        $('#errorContent').html('');
                        $.each(err.error, function(key, value) {
                            $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                        });
                        $('#saveBtnVer').html("{{ __('save-changes') }}");
                    }
                });
        }
    });
  });
</script>

@endsection
