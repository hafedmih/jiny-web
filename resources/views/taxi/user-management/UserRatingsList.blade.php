@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('user-rating-list') }} </h5>
            <div class="header-elements">
                
            </div>
        </div>
    </div>
    
    <div class="card" id="tableDiv">
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('trips') }}</th>
                    <th>{{ __('rating') }}</th>
                    <th>{{ __('feedback') }}</th>
                </tr>
            </thead>
            <tbody>
                @if (is_array($ratings_list) || is_object($ratings_list))
                @foreach($ratings_list as $key => $docum)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $docum->request ? $docum->request->request_number : '' !!}</td>
                        <td>{!! round($docum->rating, 2) !!}</td>
                        <td>{!! $docum->feedback ? $docum->feedback : 'No Feedbacks' !!}</td>
                    </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

</div>
<!-- /horizontal form modal -->

@endsection
