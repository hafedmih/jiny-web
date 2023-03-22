@extends('layouts.app')

@section('content')

<div class="content">
    <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ __('cancel-request-list') }}</h5>
                <div class="header-elements">
                    <div class="list-icons">
                    </div>
                </div>
            </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('s.no') }}</th>
                    <th>{{ __('user_details') }}</th>
                    <th>{{ __('pickup')}}</th>
                    <th>{{ __('drop')}}</th>
                    <th>{{ __('type')}}</th>
                    <th>{{ __('Date')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $key => $list)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>
                            <div><a style="color:#000" href="{{ route('userView',$list ?  ($list->userDetail ? $list->userDetail->slug : '') : '')}}">
                            {!! $list ? ($list->userDetail ? $list->userDetail->firstname.' '.$list->userDetail->lastname : '') : '' !!}</a>
                            <br>
                            {!! $list ? ($list->userDetail ? $list->userDetail->phone_number  : '') : '' !!}
                            </div>
                        </td>
                        <td>{!! $list->pick_up !!}</td>
                        <td>{!! $list->drop !!}</td>
                        <td>{!! $list->trip_type !!}</td>
                        <td>{!! $list->datetime !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>    
    </div>
    
</div>



@endsection