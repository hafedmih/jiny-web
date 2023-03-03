@extends('layouts.app')

@section('content')


<div class="content">
    <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('package-management') }}</h5>
                <div class="header-elements">
                    <div class="list-icons">
                        @if(auth()->user()->can('new-subscription'))
                           <a href="{{ route('packagesave')}}"> <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i>{{ __('add-new')}}</button></a>
                        @endif
                    </div>
                </div>
            </div>
    </div>

    <div class="card" id="tableDiv">
    @if (Session::has('success'))
                    <div class="alert alert-success">
                        <ul>
                            <li>{{ Session::get('success') }}</li>
                        </ul>
                    </div>
                @endif
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('name') }}</th>
                    <th>{{ __('hours') }}</th>
                    <th>{{ __('km') }}</th>
                    <th>{{__('base package')}}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach($packageitemlist as $key => $packagelist)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $packagelist->name !!}</td> 
                        <td>{!! $packagelist->hours !!}</td>
                        <td>{!! $packagelist->km !!}</td> 
                        <td>
                            @if($packagelist->is_base_package == 'YES' )
                              <span class="badge badge-success">{{__('yes')}}</span>
                            @else
                              <span class="badge badge-danger">{{__('no')}}</span>
                            @endif  
                        </td>
                        <td>
                            @if($packagelist['status'] == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td>
                        <td>    
                             <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                              @if(auth()->user()->can('edit-driver'))
                                <a href="{{ route('packageedit',$packagelist->slug) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                @endif
                                @if(auth()->user()->can('delete-promocode'))
                                <a href="#" onclick="Javascript: return deleteAction('$packagelist->slug', `{{ route('packagedelete',$packagelist->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                @endif
                                @if(auth()->user()->can('status-change-promocode'))
                                <a href="#" onclick="Javascript: return activeAction(`{{ route('packageactive',$packagelist->slug) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
                                @endif
                            </div>     
                        </td>

                       
                    </tr>
                @endforeach
            </tbody>
        </table>    
    </div>
  
<script>
    var message = "{{session()->get('message')}}";
    var status = "{{session()->get('status')}}";

    if(message && status == true){
        swal({
            title: "{{ __('success') }}",
            text: message,
            icon: "success",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

    if(message && status == false){
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