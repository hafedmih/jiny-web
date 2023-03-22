@extends('layouts.app')
@section('content')

    <link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrapnew.css') }}" />
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<style>
    .popover{
        top: 104.953px;
        left: 0.9766px !important;fine
        display: block;
    }

</style>
<!-- Content area -->
<div class="content">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Language Management</h5>
            <div class="header-elements">
                <div class="list-icons">
                @if(auth()->user()->can('add-new-translation'))
                    <button type="button" data-toggle="modal" data-target="#modal_theme_primary" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                @endif    
                @if(auth()->user()->can('translation-list'))
                    <button type="button" class="btn bg-pink btn-sm legitRipple"><i class="icon-list2 mr-2"></i> {{ __('list') }}</button>
                @endif    
                </div>
            </div>
        </div>
    </div>

    <!-- Form inputs -->
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('list') }}</h5>
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
                <li class="nav-item"><a href="#top-justified-tab1" class="nav-link active" data-toggle="tab">{{ __('Web') }}</a></li>
                <li class="nav-item"><a href="#top-justified-tab2" class="nav-link" data-toggle="tab">{{ __('Mobile') }}</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active in" id="top-justified-tab1"> 
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>Key</th>
                                @if($languages->count() > 0)
                                    @foreach($languages as $language)
                                    @if($language-> status ==1)
                                        <th>{{ $language->name }}({{ $language->code }})</th>
                                        @endif
                                    @endforeach
                                @endif
                                <th width="80px;">{{ __('action') }}</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @if($columnsCount > 0)
                        
                                @foreach($columns[0] as $columnKey => $columnValue)
                                    <tr>
                                        <td>
                                            <a href="#" class="translate-key" data-title="Enter Key" data-type="text" data-pk="{{ $columnKey }}" data-url="{{ route('translation.update.json.key') }}">{{ $columnKey }}</a>
                                        </td>
                                        @for($i=1; $i<=$columnsCount; ++$i)
                                            <td>
                                                <a href="#" data-title="Enter Translate" class="translate" data-code="{{ $columns[$i]['lang'] }}" data-type="textarea" data-pk="{{ $columnKey }}" data-url="{{ route('translation.update.json') }}">{{ isset($columns[$i]['data'][$columnKey]) ? $columns[$i]['data'][$columnKey] : '' }}</a>
                                            </td>
                                        @endfor
                                        <td>
                                        @if(auth()->user()->can('delete-translation'))
                                            <button data-action="{{ route('translations.destroy', $columnKey) }}" class="btn btn-danger btn-xs remove-key"><i class="icon-trash"></i></button>
                                        @endif    
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="top-justified-tab2">
                    <table class="table datatable-button-print-columns1" id="roletable">
                        <thead>
                            <tr>
                                <th>Key</th>
                                @if($languages->count() > 0)
                                    @foreach($languages as $language)
                                    @if($language->status == 1)
                                        <th>{{ $language->name }}({{ $language->code }})</th>
                                    @endif
                                    @endforeach
                                @endif
                                <th width="80px;">{{ __('action') }}</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @if($columnsCount > 0)
                                @foreach($column[0] as $columnKey => $columnValue)
                                    <tr>
                                        <td>
                                            <a href="#" class="translate-key" data-title="Enter Key" data-type="text" data-pk="{{ $columnKey }}" data-url="{{ route('translation.update.mobile.json.key') }}">{{ $columnKey }}</a>
                                        </td>
                                        @for($i=1; $i<=$columnsCount; ++$i)
                                            <td>
                                                <a href="#" data-title="Enter Translate" class="translate" data-code="{{ $column[$i]['lang'] }}" data-type="textarea" data-pk="{{ $columnKey }}" data-url="{{ route('translation.update.mobile.json') }}">{{ isset($column[$i]['data'][$columnKey]) ? $column[$i]['data'][$columnKey] : '' }}</a>
                                            </td>
                                        @endfor
                                        <td>
                                        @if(auth()->user()->can('delete-translation'))
                                            <button data-action="{{ route('translations.mobile.destroy', $columnKey) }}" class="btn btn-danger btn-xs remove-key"><i class="icon-trash"></i></button>
                                        @endif    
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /form inputs -->


     <!-- Primary modal -->
     <div id="modal_theme_primary" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">{{ __('add-new') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="{{ route('translations.create') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group row required">
                            <label class="col-form-label col-lg-4">{{ __('key') }}</label>
                            <div class="col-lg-8">
                                <input type="text" name="key" class="form-control" placeholder="Enter Key...">
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-lg-4">{{ __('value') }}</label>
                            <div class="col-lg-8">
                                <input type="text" name="value" class="form-control" placeholder="Enter Value...">
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-lg-4">{{ __('Application') }}</label>
                            <div class="col-lg-8">
                                <select class="form-control" name="application" id="application">
			                        <option value="">{{ __('select') }}</option>
			                        <option value="1">{{ __('web') }}</option>
			                        <option value="0">{{ __('mobile') }}</option>
		                        </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" class="btn bg-primary">{{ __('save-changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /primary modal -->

</div>
<!-- /content area -->
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $('.translate').editable({
        params: function(params) {
            params.code = $(this).editable().data('code');
            return params;
        }
    });


    $('.translate-key').editable({
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Key is required';
            }
        }
    });


    $('body').on('click', '.remove-key', function(){
        var cObj = $(this);


        if (confirm("{{ __('are-you-sure-want-remove') }}")) {
            $.ajax({
                url: cObj.data('action'),
                method: 'DELETE',
                success: function(data) {
                    cObj.parents("tr").remove();
                    alert("{{ __('your-imainary-file-deleted') }}");
                }
            });
        }


    });

    $("#searchKey").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
</script>
@stop