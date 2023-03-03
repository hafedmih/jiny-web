@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('user-complaints-list') }} </h5>
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
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
<!-- /horizontal form modal -->


@endsection
