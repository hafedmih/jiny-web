@extends('layouts.app')

@section('content')

<div class="content">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('roles-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <!-- <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> Add New</button>
                    <a  class="btn bg-pink btn-sm legitRipple"><i class="icon-list2 mr-2"></i> List</a> -->
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="tableDiv">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('roles-management') }} to {{$role->slug}}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a type="button" href="{{ route('roles.index') }}" id="add_new_btn" class="btn bg-pink btn-sm legitRipple"><i class="icon-list2 mr-2"></i> {{ __('role_list') }}</a>
                    <a class="list-icons-item" data-action="collapse"></a>
                    <!-- <a class="list-icons-item" data-action="reload"></a> -->
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @include('flash::message')
            @if(count($role_permissions) <= 0)
                <div class="text-center">
                    <h4 class="m-t-50"> <i class="fa fa-frown-o text-danger"></i>{{ __('no-record-found') }}</h4>
                </div>
            @else
                @foreach($permission_category as $category)
                    <legend class="text-uppercase font-size-sm font-weight-bold">{{ $category }}</legend>  
                    <ul class="">
                        <li class="">
                            @foreach($role_permissions as $permission)
                                @if($permission->category == $category)
                            <a class="mr-15" href="{{ url('/roles/'.$role->slug.'/'.$permission->name) }}">
                                @if($permission->assigned)
                                <i class="icon-checkbox-partial" aria-hidden="true"></i>
                                @else
                                <i class="icon-checkbox-unchecked" aria-hidden="true"></i>
                                @endif
                                <label class="">{{ $permission->name }}</label>
                            </a>
                                @endif
                            @endforeach
                        </li>  
                    </ul>                               
                @endforeach                                
            @endif             
        </div>
    </div>
</div>

@endsection
