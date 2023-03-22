@extends('layouts.log')

@section('content')
<div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('error_log') }}</h5>
            <div class="header-elements">
                
            </div>
        </div>
    </div>
<div class="container-fluid">

  <div class="row">
    <div class="col-md-3">
      <div class="card">

        <div>
          <input class="custom-control-input" id="darkSwitch">
          <label class="custom-control-label"  style="margin-top: 6px; padding:5px; text:bold;"></label>
        </div>

        <div class="list-group div-scroll">
          @foreach($folders as $folder)
            <div class="list-group-item">
              <?php
              \Rap2hpoutre\LaravelLogViewer\LaravelLogViewer::DirectoryTreeStructure( $storage_path, $structure );
              ?>

            </div>
          @endforeach
         
          <div class="table">
						<table class="table table-xs">
							<thead>
								<tr>
									<th>{{ __('log_files')}}</th>
								</tr>
							</thead>
							<tbody>
                @foreach($files as $file)
                  <tr>
                    <td>
                      <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}"
                        class="list-group-item @if ($current_file == $file) llv-active @endif" style="color: black;">
                        {{$file}}
                      </a>
                    </td>
                  </tr>
                @endforeach
							</tbody>
						</table>
					</div>
        </div>
      </div>
    </div>
    <div class="col-md-9 table-container">
      <div class="card">
        <div class="p-3">
          @if($current_file)
            <div class="dt-buttons" style="margin-bottom:0">
              <a class="dt-button buttons-print btn bg-blue legitRipple" tabindex="0" aria-controls="DataTables_Table_2" href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}"><span><i class="icon-download position-left"></i> Download </span></a>
              <a class="dt-button buttons-copy buttons-html5 btn bg-green legitRipple" tabindex="0" aria-controls="DataTables_Table_2" href="?clean={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}"><span><i class="icon-eraser2 position-left"></i> Clean </span></a>
              <a class="dt-button buttons-copy buttons-html5 btn bg-danger legitRipple" tabindex="0" aria-controls="DataTables_Table_2" href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}"><span><i class="icon-trash position-left"></i> Delete </span></a>
              @if(count($files) > 1)
                  <a class="dt-button buttons-copy buttons-html5 btn bg-orange legitRipple" tabindex="0" aria-controls="DataTables_Table_2" href="?delall=true{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}"><span><i class="icon-trash-alt position-left"></i> Delete All </span></a>
              @endif
            </div>
          @endif
        </div>
        @if ($logs === null)
          <div>
            Log file >50M, please download it.
          </div>
        @else
          <table id="table-log" class="table table-striped" data-ordering-index="{{ $standardFormat ? 2 : 0 }}">
            <thead>
            <tr>
              @if ($standardFormat)
                <th>Level</th>
                <th>Context</th>
                <th>Date</th>
              @else
                <th>Line number</th>
              @endif
              <th>Content</th>
            </tr>
            </thead>
            <tbody>

            @foreach($logs as $key => $log)
              <tr data-display="stack{{{$key}}}">
                @if ($standardFormat)
                  <td class="nowrap text-{{{$log['level_class']}}}">
                    <span class="fa fa-{{{$log['level_img']}}}" aria-hidden="true"></span>&nbsp;&nbsp;{{$log['level']}}
                  </td>
                  <td class="text">{{$log['context']}}</td>
                @endif
                <td class="date">{{{$log['date']}}}</td>
                <td class="text">
                  @if ($log['stack'])
                    <button type="button"
                            class="float-right expand btn btn-outline-dark btn-sm mb-2 ml-2"
                            data-display="stack{{{$key}}}">
                      <span class="icon-search4"></span>
                    </button>
                  @endif
                  {{{$log['text']}}}
                  @if (isset($log['in_file']))
                    <br/>{{{$log['in_file']}}}
                  @endif
                  @if ($log['stack'])
                    <div class="stack" id="stack{{{$key}}}"
                        style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach

            </tbody>
          </table>
        @endif
        
      </div>
    </div>
  </div>
</div>

<!-- Datatables -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>

<script>

  // dark mode by https://github.com/coliff/dark-mode-switch
  const darkSwitch = document.getElementById('darkSwitch');

  // this is here so we can get the body dark mode before the page displays
  // otherwise the page will be white for a second... 
  initTheme();

  window.addEventListener('load', () => {
    if (darkSwitch) {
      initTheme();
      darkSwitch.addEventListener('change', () => {
        resetTheme();
      });
    }
  });

  // end darkmode js
        
  $(document).ready(function () {
    $('.table-container tr').on('click', function () {
      $('#' + $(this).data('display')).toggle();
    });
    $('#table-log').DataTable({
      "order": [$('#table-log').data('orderingIndex'), 'desc'],
      "stateSave": true,
      "stateSaveCallback": function (settings, data) {
        window.localStorage.setItem("datatable", JSON.stringify(data));
      },
      "stateLoadCallback": function (settings) {
        var data = JSON.parse(window.localStorage.getItem("datatable"));
        if (data) data.start = 0;
        return data;
      }
    });
    $('#delete-log, #clean-log, #delete-all-log').click(function () {
      return confirm('Are you sure?');
    });
  });
</script>

@endsection