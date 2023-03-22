@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('user-refernce-list') }} </h5>
            <div class="header-elements">
                
            </div>
        </div>
    </div>
    
    <div class="card" id="tableDiv">
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('name') }}</th>
                    <th>{{ __('email') }}</th>
                    <th>{{ __('phone_number') }}</th>
                    <th>{{ __('amount') }}</th>
                   
                    <!-- <th>{{ __('status') }}</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($refernce_list as $key => $docum)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $docum->firstname.' '.$docum->lastname !!} ({{$docum->user_role}})</td>
                        <td>{!! $docum->email !!}</td>
                        <td>{!! $docum->phone_number !!}</td>
                        <td>{!! $docum->referal_amount !!}</td>                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection