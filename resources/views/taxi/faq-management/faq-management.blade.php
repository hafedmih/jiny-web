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
            <h5 class="card-title">{{ __('faq-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-faq'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Faq list</h6>
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
                                    <th>{{ __('Question') }}</th>
                                    <th>{{ __('Answer') }}</th>
                                    <th>{{ __('category') }}</th>
                                    <th>{{ __('status') }}</th>
                                    <th>{{ __('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(is_array($column) || is_object($column))
                            @php($key = 0)
                                @foreach($column["$lang->code"] as $value )
                                    <tr>  
                                        <td>{{ ++$key }}</td>
                                        <td>{{$value['question']}}</td>
                                        <td>{{$value['answer']}}</td>
                                        <td>{{$value['category']}}</td>
                                        <td>
                                            @if($value['status'] == 1)
                                                <span class="badge badge-success">{{ __('active') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                                            @endif
                                        </td>  
                                        <td> 
                                            <a class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7" ></i></a>
                                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: relative; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                                @if(auth()->user()->can('edit-faq'))
                                                <a href="#" onclick="Javascript: return editAction(`{{ route('faq-managementEdit', $value['slug']) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                                @endif
                                                @if(auth()->user()->can('delete-faq'))
                                                <a href="#" onclick="Javascript: return deleteAction('$value->slug', `{{ route('faq-managementDelete',  $value['slug']) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                @endif
                                                @if(auth()->user()->can('status-change-faq'))
                                                <a href="#" onclick="Javascript: return activeAction( `{{ route('faq-managementChangeStatus',  $value['slug']) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
                                                @endif
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
    </div>

    <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" id="langForm" name="roleForm" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                            <span id="errorContent"></span>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('Question') }}</label>
                            <div class="col-sm-9">
                                <textarea placeholder="Question" id="question" class="form-control" name="question"></textarea>
                                <input type="hidden" name="faq_id" id="faq_id">
                            </div>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('Answer') }}</label>
                            <div class="col-sm-9">
                                <textarea placeholder="Answer" id="answer" class="form-control" name="answer"></textarea>
                            </div> 
                        </div>
                        <div class="form-group row hidding required">
                            <label class="col-form-label col-sm-3">{{ __('category') }}</label>
                            <div class="col-sm-9">
                                <select id="category" class="form-control" name="category">
			                        <option value="">Select Category</option>
                                    <option value="user">{{ __('user') }}</option>
                                    <option value="driver">{{ __('driver') }}</option>
			                        <option value="support">{{ __('support') }}</option>
			                        <option value="help">{{ __('help') }}</option>
			                    </select>
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
                $('#modelHeading').html("{{ __('edit-faq-management') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit-faq-management");
                $('#saveBtn').show();
                $('#roleModel').modal('show');
                $('#faq_id').val(data.key);
                $('#question').val(data.faq.question);
                $('#answer').val(data.faq.answer);
                $('#category').val(data.faq.category);
                $('#language').val(data.faq.language);
                $('.hidding').show();
                $('#status').val(data.faq.status);
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
                $('#modelHeading').html("{{ __('view-faq-management') }}");
                $('#errorbox').hide();
                $('#saveBtn').hide();
                $('#roleModel').modal('show');
                $('#faq_id').val(data.key.slug);
                $('#question').val(data.faq.question);
                $('#answer').val(data.faq.answer);
                $('.hidding').hide();
                $('#question').attr('readonly', true);
                $('#answer').attr('readonly', true);
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
        $('#faq_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-faq') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_faq-management");
        $('#errorbox').hide();
        $(".hidding").show();
    });

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
        
        if(btnVal == 'edit-faq-management'){
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('faq-managementUpdate') }}",
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
                                // $("#reloadDiv").load("{{ route('faq-management') }}");
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
                url: "{{ route('faq-managementSave') }}",
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
                                // $("#reloadDiv").load("{{ route('faq-management') }}");
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
        }
    });

  });
</script>

@endsection
